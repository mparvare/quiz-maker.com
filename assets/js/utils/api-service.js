const ApiService = {
    baseUrl: 'http://localhost:3000/api', // تنظیم آدرس بک‌اند

    async request(endpoint, method = 'GET', data = null, requiresAuth = false) {
        const url = this.baseUrl + endpoint;
        
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include' // برای پشتیبانی از کوکی‌ها
        };
        
        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'درخواست با خطا مواجه شد');
            }
            
            if (response.status === 204 || method === 'HEAD') {
                return null;
            }
            
            return await response.json();
        } catch (error) {
            console.error('خطا در ارسال درخواست:', error);
            throw error;
        }
    },

    // احراز هویت
    async login(email, password) {
        return this.request('/auth/login', 'POST', { email, password }, false);
    },

    async register(userData) {
        return this.request('/auth/register', 'POST', userData, false);
    },

    async logout() {
        return this.request('/auth/logout', 'POST', null, false);
    },

    // احراز هویت و وضعیت کاربر
    async checkAuthStatus() {
        return this.request('/auth/status', 'GET', null, false);
    },

    // آزمون‌ها
    async getQuizzes(params = {}) {
        const queryParams = new URLSearchParams();
        Object.keys(params).forEach(key => {
            if (params[key]) queryParams.append(key, params[key]);
        });
        
        const queryString = queryParams.toString();
        return this.request(`/quizzes${queryString ? '?' + queryString : ''}`, 'GET');
    },

    async getQuizDetails(quizId) {
        return this.request(`/quizzes/${quizId}`, 'GET');
    },

    async createQuiz(quizData) {
        return this.request('/quizzes', 'POST', quizData);
    },

    async updateQuiz(quizId, quizData) {
        return this.request(`/quizzes/${quizId}`, 'PUT', quizData);
    },

    // نتایج آزمون
    async submitQuizResult(quizId, resultData) {
        return this.request(`/quizzes/${quizId}/submit`, 'POST', resultData);
    },

    async getQuizResults(quizId) {
        return this.request(`/quizzes/${quizId}/results`, 'GET');
    },

    // پروفایل کاربر
    async getUserProfile() {
        return this.request('/user/profile', 'GET');
    },

    async updateUserProfile(profileData) {
        return this.request('/user/profile', 'PUT', profileData);
    },

    // مدیریت خطا
    handleError(error) {
        console.error('خطای سرور:', error);
        
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    // احراز هویت ناموفق
                    this.redirectToLogin();
                    break;
                case 403:
                    // دسترسی غیرمجاز
                    alert('شما اجازه دسترسی به این بخش را ندارید.');
                    break;
                case 404:
                    // منبع یافت نشد
                    alert('منبع مورد نظر یافت نشد.');
                    break;
                case 500:
                    // خطای سرور
                    alert('خطای سرور، لطفاً بعداً تلاش کنید.');
                    break;
                default:
                    alert('خطای نامشخص رخ داده است.');
            }
        } else if (error.message) {
            alert(error.message);
        }
    },

    redirectToLogin() {
        window.location.href = '/login.html';
    }
};

// صادر کردن سرویس برای استفاده در سایر ماژول‌ها
export default ApiService;