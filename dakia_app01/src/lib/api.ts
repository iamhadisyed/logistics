import axios from 'axios';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

import { getSession } from 'next-auth/react';

// Create axios instance with default config
const apiClient = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true, // Important for Sanctum cookie-based auth
});

// Add request interceptor to include Bearer token
apiClient.interceptors.request.use(async (config) => {
    const session = await getSession();
    if (session?.user?.accessToken) {
        config.headers.Authorization = `Bearer ${session.user.accessToken}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

// Legacy Consignment API (kept for backward compatibility with old pages)
export const consignmentApi = {
    list: (params?: any) => apiClient.get('/shipments', { params }),

    create: (data: any) => apiClient.post('/shipments', data),

    show: (id: number) => apiClient.get(`/shipments/${id}`),

    update: (id: number, data: any) => apiClient.put(`/shipments/${id}`, data),

    delete: (id: number) => apiClient.delete(`/shipments/${id}`),

    bulkUpload: (file: File) => {
        const formData = new FormData();
        formData.append('file', file);
        return apiClient.post('/shipments/bulk-upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    },

    bulkUpdate: (consignmentIds: number[], status: number) =>
        apiClient.post('/shipments/bulk-update', { consignment_ids: consignmentIds, status }),
};



// Shipment API
export const shipmentApi = {
    list: (params?: any) => apiClient.get('/shipments', { params }),

    store: (data: any) =>
        apiClient.post('/shipments', data),

    show: (id: number) => apiClient.get(`/shipments/${id}`),

    generateLabel: (id: number) => apiClient.post(`/shipments/${id}/generate-label`),
};

// Carrier API
export const carrierApi = {
    list: (activeOnly?: boolean) =>
        apiClient.get('/carriers', { params: activeOnly ? { active_only: true } : {} }),

    show: (id: number) => apiClient.get(`/carriers/${id}`),

    create: (data: import('@/types/carrier').CreateCarrierPayload) => apiClient.post('/carriers', data),

    update: (id: number, data: import('@/types/carrier').UpdateCarrierPayload) =>
        apiClient.put(`/carriers/${id}`, data),

    updateStatus: (id: number, status: 'active' | 'inactive' | 'delete') =>
        apiClient.post(`/carriers/${id}/status`, { status }),
};

// Service API
export const serviceApi = {
    list: (params?: any) => apiClient.get('/services', { params }),

    available: (originCountry: number, destinationCountry: number, weight: number) =>
        apiClient.get('/services/available', {
            params: { origin_country: originCountry, destination_country: destinationCountry, weight }
        }),

    show: (id: number) => apiClient.get(`/services/${id}`),

    create: (data: any) => apiClient.post('/services', data),

    update: (id: number, data: any) => apiClient.put(`/services/${id}`, data),
};


// Country API
export const countryApi = {
    list: (all?: boolean) => apiClient.get('/countries', { params: { all } }),
    show: (id: number) => apiClient.get(`/countries/${id}`),
};

// User API
export const userApi = {
    list: () => apiClient.get('/users'),
    show: (id: number) => apiClient.get(`/users/${id}`),
    update: (id: number, data: any) => apiClient.put(`/users/${id}`, data),
    delete: (id: number) => apiClient.delete(`/users/${id}`),
    getPermissions: (id: number) => apiClient.get(`/users/${id}/permissions`),
};

export default apiClient;
