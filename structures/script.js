// script.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing script...');
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // DOM Elements
    const uploadMethod = document.getElementById('uploadMethod');
    const webcamMethod = document.getElementById('webcamMethod');
    const uploadSection = document.getElementById('uploadSection');
    const webcamSection = document.getElementById('webcamSection');
    const inferenceForm = document.getElementById('inferenceForm');
    const startWebcamBtn = document.getElementById('startWebcam');
    const captureBtn = document.getElementById('captureBtn');
    const stopWebcamBtn = document.getElementById('stopWebcam');
    const webcamElement = document.getElementById('webcam');
    const webcamCanvas = document.getElementById('webcamCanvas');
    const webcamResults = document.getElementById('webcamResults');
    
    console.log('Elements found:', {
        uploadMethod: !!uploadMethod,
        webcamMethod: !!webcamMethod,
        uploadSection: !!uploadSection,
        webcamSection: !!webcamSection,
        startWebcamBtn: !!startWebcamBtn,
        captureBtn: !!captureBtn
    });
    
    let stream = null;

    // Input Method Toggle
    uploadMethod.addEventListener('change', toggleInputMethod);
    webcamMethod.addEventListener('change', toggleInputMethod);

    function toggleInputMethod() {
        console.log('Toggle method called');
        console.log('Upload checked:', uploadMethod.checked);
        console.log('Webcam checked:', webcamMethod.checked);
        
        if (uploadMethod.checked) {
            console.log('Showing upload section');
            uploadSection.style.display = 'block';
            webcamSection.style.display = 'none';
            stopWebcam(); // Stop webcam when switching to upload
        } else if (webcamMethod.checked) {
            console.log('Showing webcam section');
            uploadSection.style.display = 'none';
            webcamSection.style.display = 'block';
            // Don't auto-start webcam, wait for user to click "Start Webcam"
        }
    }

    // Upload Form Submission
    inferenceForm.addEventListener('submit', async function(e) {
        e.preventDefault(); // Prevent default form submission
        await processUploadedImages();
    });

    // Webcam Functions
    startWebcamBtn.addEventListener('click', startWebcam);
    captureBtn.addEventListener('click', captureImage);
    stopWebcamBtn.addEventListener('click', stopWebcam);

    async function startWebcam() {
        console.log('Start webcam clicked');
        try {
            // Check if browser supports mediaDevices
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Webcam access is not supported in your browser. Please use Chrome, Firefox, or Edge.');
                return;
            }

            console.log('Starting webcam...');
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'environment' // Use back camera if available
                } 
            });
            
            webcamElement.srcObject = stream;
            startWebcamBtn.disabled = true;
            captureBtn.disabled = false;
            stopWebcamBtn.disabled = false;
            
            console.log('Webcam started successfully');
            
        } catch (err) {
            console.error('Error accessing webcam:', err);
            let errorMessage = 'Cannot access webcam: ';
            
            if (err.name === 'NotAllowedError') {
                errorMessage += 'Please allow camera permissions and try again.';
            } else if (err.name === 'NotFoundError') {
                errorMessage += 'No camera found on your device.';
            } else if (err.name === 'NotSupportedError') {
                errorMessage += 'Your browser does not support camera access.';
            } else {
                errorMessage += err.message;
            }
            
            alert(errorMessage);
            startWebcamBtn.disabled = false;
        }
    }

    function stopWebcam() {
        console.log('Stop webcam clicked');
        if (stream) {
            stream.getTracks().forEach(track => {
                track.stop();
            });
            stream = null;
            console.log('Webcam stopped');
        }
        webcamElement.srcObject = null;
        startWebcamBtn.disabled = false;
        captureBtn.disabled = true;
        stopWebcamBtn.disabled = true;
        webcamResults.innerHTML = ''; // Clear previous results
    }

    function captureImage() {
        console.log('Capture image clicked');
        if (!stream) {
            alert('Please start the webcam first.');
            return;
        }

        try {
            const context = webcamCanvas.getContext('2d');
            webcamCanvas.width = webcamElement.videoWidth;
            webcamCanvas.height = webcamElement.videoHeight;
            
            context.drawImage(webcamElement, 0, 0, webcamCanvas.width, webcamCanvas.height);
            
            // Convert canvas to blob and process
            webcamCanvas.toBlob(async function(blob) {
                if (blob) {
                    await processWebcamImage(blob);
                } else {
                    alert('Failed to capture image from webcam.');
                }
            }, 'image/jpeg', 0.8);
        } catch (error) {
            console.error('Error capturing image:', error);
            alert('Error capturing image: ' + error.message);
        }
    }

    async function processWebcamImage(blob) {
        const loadingDiv = document.getElementById('loading');
        
        loadingDiv.style.display = 'block';
        loadingDiv.innerHTML = 'Processing webcam image...';
        captureBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('images[]', blob, 'webcam-capture.jpg');

            // Use Laravel route with CSRF token
            const response = await fetch('/infer', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`API Error (${response.status}): ${response.statusText}`);
            }

            const results = await response.json();
            console.log('API Response:', results);

            if (results && results.length > 0) {
                displayWebcamResult(results[0], blob);
            } else {
                throw new Error('No results returned from API');
            }
        } catch (error) {
            console.error('Error processing webcam image:', error);
            webcamResults.innerHTML = `<div class="alert alert-danger">Error processing image: ${error.message}</div>`;
        } finally {
            loadingDiv.style.display = 'none';
            captureBtn.disabled = false;
        }
    }

    function displayWebcamResult(result, originalBlob) {
        const resultItemDiv = document.createElement('div');
        resultItemDiv.className = 'result-item';

        if (result.error) {
            resultItemDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h3>Webcam Result:</h3>
                    <p>${result.error}</p>
                </div>`;
        } else if (result.prediction) {
            processPredictionResult(resultItemDiv, result, originalBlob, 'Webcam Capture');
        } else {
            resultItemDiv.innerHTML = `
                <div class="alert alert-warning">
                    <h3>Webcam Result:</h3>
                    <p>Unexpected response format.</p>
                </div>`;
        }

        webcamResults.innerHTML = '';
        webcamResults.appendChild(resultItemDiv);
        
        // Scroll to results
        resultItemDiv.scrollIntoView({ behavior: 'smooth' });
    }

    // Upload Image Processing
    async function processUploadedImages() {
        const fileInput = document.getElementById('imageInput');
        const runButton = document.getElementById('runButton');
        const loadingDiv = document.getElementById('loading');
        const resultContainer = document.getElementById('resultContainer');

        const files = fileInput.files;

        if (files.length === 0) {
            alert('Please select at least one image file.');
            return;
        }

        if (files.length > 10) {
            alert('Too many files selected. Maximum 10 allowed.');
            return;
        }

        loadingDiv.innerHTML = `Processing... 0/${files.length} images completed.`;
        loadingDiv.style.display = 'block';
        runButton.disabled = true;
        resultContainer.innerHTML = '';

        try {
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            // Use Laravel route with CSRF token
            const response = await fetch('/infer', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`API Error (${response.status}): ${response.statusText}`);
            }

            const results = await response.json();
            
            results.forEach((item, index) => {
                loadingDiv.innerHTML = `Processing... ${index + 1}/${files.length} images completed.`;
                
                const resultItemDiv = document.createElement('div');
                resultItemDiv.className = 'result-item';

                if (item.error) {
                    resultItemDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h3>Result for ${item.filename}:</h3>
                            <p>${item.error}</p>
                        </div>`;
                } else if (item.prediction) {
                    processPredictionResult(resultItemDiv, item, files[index], item.filename);
                } else {
                    resultItemDiv.innerHTML = `
                        <div class="alert alert-warning">
                            <h3>Result for ${item.filename}:</h3>
                            <p>Unexpected response format.</p>
                        </div>`;
                }

                resultContainer.appendChild(resultItemDiv);
            });
        } catch (error) {
            console.error('Error:', error);
            resultContainer.innerHTML = `<div class="alert alert-danger"><h2>Error: ${error.message}</h2></div>`;
        } finally {
            loadingDiv.style.display = 'none';
            runButton.disabled = false;
        }
    }

    // Common function to process prediction results
    function processPredictionResult(resultItemDiv, item, file, filename) {
        const prediction = item.prediction;

        console.log(`Prediction for ${filename}:`, prediction);

        let predictions = [];
        let processedImage = null;
        let borderClass = '';

        if (prediction.outputs && Array.isArray(prediction.outputs) && prediction.outputs[0]) {
            const output = prediction.outputs[0];
            
            if (output.predictions && output.predictions.predictions && Array.isArray(output.predictions.predictions)) {
                predictions = output.predictions.predictions;
            }
            
            if (output.label_visualization && output.label_visualization.value) {
                processedImage = output.label_visualization.value;
            } else if (output.bounding_box_visualization && output.bounding_box_visualization.value) {
                processedImage = output.bounding_box_visualization.value;
            }
        }

        console.log('Extracted predictions:', predictions);

        let message = '';
        let preventiveMeasures = '';

        if (predictions && Array.isArray(predictions) && predictions.length > 0) {
            const sigatokaPred = predictions.find(p => 
                p.class && p.class.toLowerCase().includes('sigatoka')
            );
            const panamaPred = predictions.find(p => 
                p.class && p.class.toLowerCase().includes('panama')
            );
            const healthyPred = predictions.find(p => 
                p.class && p.class.toLowerCase().includes('healthy')
            );

            if (panamaPred) {
                message = `The banana leaf was infected by Panama. (Confidence: ${Math.round(panamaPred.confidence * 100)}%)`;
                borderClass = 'panama-border';
                preventiveMeasures = `
                    <div class="preventive-measures panama">
                        <h4>Preventive Measures for Panama Disease:</h4>
                        <ul>
                            <li>Use certified disease-free planting materials.</li>
                            <li>Implement strict quarantine measures to prevent introduction of pathogens.</li>
                            <li>Improve drainage to reduce soil moisture and prevent root rot.</li>
                            <li>Apply fungicides specifically labeled for Panama wilt.</li>
                            <li>Rotate crops and avoid continuous cultivation of susceptible hosts.</li>
                            <li>Remove and destroy infected plants immediately.</li>
                            <li>Consider resistant banana varieties if available.</li>
                        </ul>
                    </div>
                `;
            } else if (sigatokaPred) {
                message = `The banana leaf was infected by Sigatoka. (Confidence: ${Math.round(sigatokaPred.confidence * 100)}%)`;
                borderClass = 'sigatoka-border';
                preventiveMeasures = `
                    <div class="preventive-measures">
                        <h4>Preventive Measures for Sigatoka Disease:</h4>
                        <ul>
                            <li>Apply fungicides containing copper or chlorothalonil regularly.</li>
                            <li>Ensure proper spacing between plants to improve air circulation.</li>
                            <li>Remove and destroy infected leaves immediately.</li>
                            <li>Water at the base of the plant to avoid wetting the foliage.</li>
                            <li>Rotate fungicide applications to prevent resistance.</li>
                        </ul>
                    </div>
                `;
            } else if (healthyPred) {
                message = `The banana leaf is healthy. (Confidence: ${Math.round(healthyPred.confidence * 100)}%)`;
                borderClass = 'healthy-border';
            } else {
                const bestPrediction = predictions.reduce((best, current) => 
                    (!best || current.confidence > best.confidence) ? current : best, null
                );
                if (bestPrediction) {
                    message = `Detected: ${bestPrediction.class} (Confidence: ${Math.round(bestPrediction.confidence * 100)}%)`;
                } else {
                    message = "No clear disease detection. The leaf may be healthy or have an unknown condition.";
                }
            }
        } else {
            message = "No predictions returned from the model.";
        }

        // Create ug result sa ubos sa image
        let resultHTML = `
            <div class="card ${borderClass}">
                <div class="card-body">
                    <h3 class="card-title">Result for ${filename}</h3>
                    <p class="card-text">${message}</p>
        `;

        if (preventiveMeasures) {
            resultHTML += preventiveMeasures;
        }

        resultHTML += `</div></div>`;

        resultItemDiv.innerHTML = resultHTML;

        // Add ug image diri
        if (processedImage) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'text-center my-3';
            const imgElement = document.createElement('img');
            imgElement.src = 'data:image/jpeg;base64,' + processedImage;
            imgElement.alt = `Processed Image ${filename}`;
            imgElement.className = 'img-fluid rounded shadow';
            imgElement.style.maxWidth = '100%';
            imgContainer.appendChild(imgElement);
            resultItemDiv.insertBefore(imgContainer, resultItemDiv.firstChild);
        } else {
            // Show original image for webcam or file uploads
            const imgContainer = document.createElement('div');
            imgContainer.className = 'text-center my-3';
            const imgElement = document.createElement('img');
            
            if (file instanceof Blob) {
                imgElement.src = URL.createObjectURL(file);
            } else {
                imgElement.src = URL.createObjectURL(file);
            }
            
            imgElement.alt = `Original Image ${filename}`;
            imgElement.className = 'img-fluid rounded shadow';
            imgElement.style.maxWidth = '100%';
            imgContainer.appendChild(imgElement);
            resultItemDiv.insertBefore(imgContainer, resultItemDiv.firstChild);
        }
    }

    // Initialize the page with upload method selected
    console.log('Initializing page...');
    toggleInputMethod();
});
