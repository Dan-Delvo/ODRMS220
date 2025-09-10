/**
 * Offline Request Manager for Document Requests
 * Handles storing and syncing offline document requests
 */
class OfflineRequestManager {
    constructor() {
        this.dbName = 'UBNHSDocumentRequestsDB';
        this.version = 1;
        this.storeName = 'offline_requests';
        this.db = null;
        this.init();
    }

    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onerror = () => {
                console.error('IndexedDB initialization failed:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                console.log('IndexedDB initialized successfully');
                resolve();
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create object store for offline requests
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const store = db.createObjectStore(this.storeName, {
                        keyPath: 'offline_id',
                        autoIncrement: true
                    });

                    // Create indexes
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                    store.createIndex('synced', 'synced', { unique: false });
                    store.createIndex('email', 'email_address', { unique: false });
                }
            };
        });
    }

    async storeOfflineRequest(formData) {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const store = transaction.objectStore(this.storeName);

        const requestData = {
            // Document request information
            request_schl_entity: formData.get('request_schl_entity'),
            document_id: formData.get('document_id'),
            release_mode: formData.get('release_mode'),

            // Student information
            student_first_name: formData.get('student_first_name'),
            student_last_name: formData.get('student_last_name'),
            lrn: formData.get('lrn'),
            grade_level: formData.get('grade_level'),
            student_status: formData.get('student_status'),
            last_sy_attended: formData.get('last_sy_attended'),
            email_address: formData.get('email_address'),

            // Offline tracking
            timestamp: Date.now(),
            synced: false,
            created_offline: true,
            sync_attempts: 0
        };

        return new Promise((resolve, reject) => {
            const request = store.add(requestData);
            request.onsuccess = () => {
                console.log('Offline request stored successfully');
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('Failed to store offline request:', request.error);
                reject(request.error);
            };
        });
    }

    async getPendingRequests() {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readonly');
        const store = transaction.objectStore('offline_requests');
        const index = store.index('synced');

        return new Promise((resolve, reject) => {
            const request = index.getAll(false); // Get all unsynced requests
            request.onsuccess = () => {
                resolve(request.result);
            };
            request.onerror = () => {
                reject(request.error);
            };
        });
    }

    async markAsSynced(offlineId, serverId = null) {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const store = transaction.objectStore(this.storeName);

        return new Promise((resolve, reject) => {
            const getRequest = store.get(offlineId);

            getRequest.onsuccess = () => {
                const data = getRequest.result;
                if (data) {
                    data.synced = true;
                    data.synced_at = Date.now();
                    data.server_id = serverId;

                    const updateRequest = store.put(data);
                    updateRequest.onsuccess = () => resolve();
                    updateRequest.onerror = () => reject(updateRequest.error);
                } else {
                    reject(new Error('Record not found'));
                }
            };

            getRequest.onerror = () => reject(getRequest.error);
        });
    }

    async incrementSyncAttempts(offlineId) {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const store = transaction.objectStore(this.storeName);

        return new Promise((resolve, reject) => {
            const getRequest = store.get(offlineId);

            getRequest.onsuccess = () => {
                const data = getRequest.result;
                if (data) {
                    data.sync_attempts = (data.sync_attempts || 0) + 1;
                    data.last_sync_attempt = Date.now();

                    const updateRequest = store.put(data);
                    updateRequest.onsuccess = () => resolve();
                    updateRequest.onerror = () => reject(updateRequest.error);
                } else {
                    reject(new Error('Record not found'));
                }
            };

            getRequest.onerror = () => reject(getRequest.error);
        });
    }

    async getStoredRequestsCount() {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readonly');
        const store = transaction.objectStore(this.storeName);
        const index = store.index('synced');

        return new Promise((resolve, reject) => {
            const request = index.count(false);
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async clearSyncedRequests() {
        if (!this.db) {
            await this.init();
        }

        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const store = transaction.objectStore(this.storeName);
        const index = store.index('synced');

        return new Promise((resolve, reject) => {
            const request = index.getAll(true); // Get all synced requests

            request.onsuccess = () => {
                const syncedRequests = request.result;
                let deletePromises = syncedRequests.map(req => {
                    return new Promise((delResolve, delReject) => {
                        const deleteRequest = store.delete(req.offline_id);
                        deleteRequest.onsuccess = () => delResolve();
                        deleteRequest.onerror = () => delReject(deleteRequest.error);
                    });
                });

                Promise.all(deletePromises)
                    .then(() => resolve(syncedRequests.length))
                    .catch(reject);
            };

            request.onerror = () => reject(request.error);
        });
    }
}

// Export for use in other files
window.OfflineRequestManager = OfflineRequestManager;
