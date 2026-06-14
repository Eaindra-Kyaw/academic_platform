import { BrowserMultiFormatReader } from '@zxing/library';

class QRScanner {
    constructor() {
        this.reader = null;
        this.isScanning = false;
        this.currentCameraId = 'environment';
        this.videoElement = null;
        this.onSuccessCallback = null;
        this.onErrorCallback = null;
    }

    async initialize(videoElementId) {
        this.videoElement = document.getElementById(videoElementId);
        if (!this.videoElement) {
            throw new Error('Video element not found');
        }

        this.reader = new BrowserMultiFormatReader();
        await this.reader.listVideoInputDevices();
        return this;
    }

    async start() {
        if (this.isScanning) return;

        try {
            const devices = await this.reader.listVideoInputDevices();
            const backCamera = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('environment') ||
                device.label.toLowerCase().includes('rear')
            );

            const cameraId = this.currentCameraId === 'environment' && backCamera
                ? backCamera.deviceId
                : devices[0]?.deviceId;

            if (!cameraId) {
                throw new Error('No camera found');
            }

            await this.reader.decodeFromVideoElement(
                this.videoElement,
                cameraId,
                (result, error) => {
                    if (result && this.onSuccessCallback) {
                        this.onSuccessCallback(result.getText());
                    }
                }
            );

            this.isScanning = true;
        } catch (error) {
            console.error('Start scanner error:', error);
            if (this.onErrorCallback) {
                this.onErrorCallback(error);
            }
        }
    }

    async switchCamera() {
        this.currentCameraId = this.currentCameraId === 'environment' ? 'user' : 'environment';
        if (this.reader && this.isScanning) {
            await this.reader.reset();
            this.isScanning = false;
            await this.start();
        }
    }

    stop() {
        if (this.reader && this.isScanning) {
            this.reader.reset();
            this.isScanning = false;
        }
    }

    onSuccess(callback) {
        this.onSuccessCallback = callback;
        return this;
    }

    onError(callback) {
        this.onErrorCallback = callback;
        return this;
    }
}

export default QRScanner;
