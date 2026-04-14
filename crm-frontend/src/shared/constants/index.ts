export const API_URL = import.meta.env.VITE_API_URL;
export const AUTH_URL = import.meta.env.VITE_AUTH_URL;

export const ROUTES = {
  HOME: '/',
  CLIENTS: '/clients',
  MATERIALS: '/materials',
  PROCESSINGS: '/processings',
} as const;

export const API_ENDPOINTS = {
  AUTH: {
    REFRESH: '/auth/refresh',
    LOGOUT: '/auth/logout',
  },
  USERS: {
    BY_ID: (id: string | number) => `/users/${id}`,
  },
  CLIENTS: {
    LIST: '/clients',
    CREATE: '/clients/create',
    BY_ID: (id: string | number) => `/clients/${id}`,
    UPDATE: (id: string | number) => `/clients/update/${id}`,
    DELETE: (id: string | number) => `/clients/delete/${id}`,
    TYPES: '/clients/types',
    DOCS_TYPES: '/clients/docs-types',
    PHONE_TYPES: '/clients/phone-types',
  },
  MATERIALS: {
    LIST: '/materials',
    CREATE: '/materials/create',
    BY_ID: (id: string | number) => `/materials/${id}`,
    UPDATE: (id: string | number) => `/materials/update/${id}`,
    DELETE: (id: string | number) => `/materials/delete/${id}`,
    IMAGES: (materialId: string | number) => `/materials/${materialId}/images`,
    IMAGE_UPDATE: (imageId: string | number) => `/materials/images/${imageId}`,
    IMAGE_DELETE: (imageId: string | number) => `/materials/images/${imageId}`,
  },
  PROCESSINGS: {
    LIST: '/processings',
    CREATE: '/processings/create',
    BY_ID: (id: string | number) => `/processings/${id}`,
    UPDATE: (id: string | number) => `/processings/update/${id}`,
    DELETE: (id: string | number) => `/processings/delete/${id}`,
    TYPES: '/processings/types',
    IMAGES: (processingId: string | number) => `/processings/${processingId}/images`,
    IMAGE_UPDATE: (imageId: string | number) => `/processings/images/${imageId}`,
    IMAGE_DELETE: (imageId: string | number) => `/processings/images/${imageId}`,
  },
} as const;
