// Core Application Constants
module.exports = {
  APP: {
    NAME: 'NetBanking Platform',
    VERSION: '1.0.0',
    ENV: process.env.NODE_ENV || 'development',
    PORT: process.env.PORT || 3000,
    API_PREFIX: '/api/v1',
  },
  HTTP_STATUS: {
    OK: 200,
    CREATED: 201,
    BAD_REQUEST: 400,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    NOT_FOUND: 404,
    INTERNAL_SERVER_ERROR: 500,
  },
  ERROR_MESSAGES: {
    INTERNAL_SERVER_ERROR: 'Something went wrong!',
    ROUTE_NOT_FOUND: 'Route not found',
    VALIDATION_ERROR: 'Validation Error',
  },
  LOG_LEVELS: {
    ERROR: 'error',
    WARN: 'warn',
    INFO: 'info',
    DEBUG: 'debug',
  },
  TRANSACTION_TYPES: {
    DEBIT: 'DEBIT',
    CREDIT: 'CREDIT',
  },
  ACCOUNT_TYPES: {
    SAVINGS: 'SAVINGS',
    CURRENT: 'CURRENT',
  }
};
