/**
 * مدیریت توکن‌های JWT برای احراز هویت
 */
const TokenManager = {
    // کلیدهای ذخیره‌سازی در local storage
    ACCESS_TOKEN_KEY: 'quiz_maker_access_token',
    REFRESH_TOKEN_KEY: 'quiz_maker_refresh_token',
    
    /**
     * تنظیم توکن در local storage
     * @param {string} accessToken - توکن دسترسی
     * @param {string} refreshToken - توکن تجدید
     */
    setToken(accessToken, refreshToken = null) {
        localStorage.setItem(this.ACCESS_TOKEN_KEY, accessToken);
        if (refreshToken) {
            localStorage.setItem(this.REFRESH_TOKEN_KEY, refreshToken);
        }
    },
    
    /**
     * دریافت توکن دسترسی
     * @returns {string|null} توکن دسترسی یا null
     */
    getToken() {
        return localStorage.getItem(this.ACCESS_TOKEN_KEY);
    },
    
    /**
     * دریافت توکن تجدید
     * @returns {string|null} توکن تجدید یا null
     */
    getRefreshToken() {
        return localStorage.getItem(this.REFRESH_TOKEN_KEY);
    },
    
    /**
     * پاک کردن توکن‌ها
     */
    clearToken() {
        localStorage.removeItem(this.ACCESS_TOKEN_KEY);
        localStorage.removeItem(this.REFRESH_TOKEN_KEY);
    },
    
    /**
     * بررسی اینکه آیا کاربر احراز هویت شده است
     * @returns {boolean} وضعیت احراز هویت
     */
    isAuthenticated() {
        const token = this.getToken();
        return !!token;
    },
    
    /**
     * بررسی اعتبار توکن (به صورت ساده براساس وجود توکن)
     * توجه: بررسی واقعی اعتبار توکن نیازمند بررسی تاریخ انقضا یا تأیید از سمت سرور است
     * @returns {boolean} وضعیت اعتبار توکن
     */
    isTokenValid() {
        try {
            const token = this.getToken();
            
            if (!token) return false;
            
            // بررسی ساختار JWT (XXXX.YYYY.ZZZZ)
            const parts = token.split('.');
            if (parts.length !== 3) return false;
            
            // بررسی پی‌لود
            const payload = JSON.parse(atob(parts[1]));
            const currentTime = Math.floor(Date.now() / 1000);
            
            // اگر exp موجود باشد، بررسی زمان انقضا
            if (payload.exp && payload.exp < currentTime) {
                console.log('توکن منقضی شده است.');
                return false;
            }
            
            return true;
        } catch (e) {
            console.error('خطا در بررسی اعتبار توکن:', e);
            return false;
        }
    },
    
    /**
     * تبدیل توکن به اطلاعات کاربر
     * @returns {object|null} اطلاعات کاربر از توکن
     */
    getUserInfo() {
        try {
            const token = this.getToken();
            
            if (!token) return null;
            
            const parts = token.split('.');
            if (parts.length !== 3) return null;
            
            const payload = JSON.parse(atob(parts[1]));
            return payload;
        } catch (e) {
            console.error('خطا در استخراج اطلاعات کاربر:', e);
            return null;
        }
    }
};