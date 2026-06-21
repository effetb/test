import axios from "axios";

class ApiConnection {
    get(path) {
        return this.fetchApi("GET", path);
    }

    post(path, params = {}) {
        return this.fetchApi("POST", path, params);
    }

    async fetchApi(method, path, params = {}, secured = true) {
        if (!this.token() && secured) {
            await this.login();
        }

        const url = import.meta.env.VITE_API_URL + "/" + path;

        let headers = {
            "Content-Type": "application/json;charset=utf-8",
        };

        if (secured && this.token()) {
            headers["Authorization"] = "Bearer " + this.token();
        }

        try {
            const response = await axios({
                method: method,
                url: url,
                data: params,
                headers: headers,
            });

            return response.data;

        } catch (error) {
            console.error("API Error:", error);

            // ❌ NO reload infinito
            // Solo limpiar token si es realmente inválido
            if (error?.response?.status === 401) {
                this.clearToken();
            }

            // devolvemos error controlado
            throw error;
        }
    }

    login() {
        const credentials = {
            username: import.meta.env.VITE_API_USERNAME,
            password: import.meta.env.VITE_API_PWD,
        };

        return new Promise((resolve, reject) => {
            this.fetchApi("post", "api/login_check", credentials, false)
                .then((data) => {
                    if (data?.token) {
                        window.localStorage.setItem("token", data.token);
                        resolve(data);
                    } else {
                        reject("No token received");
                    }
                })
                .catch((err) => {
                    console.error("Login error:", err);
                    reject(err);
                });
        });
    }

    token() {
        return window.localStorage.getItem("token");
    }

    clearToken() {
        window.localStorage.removeItem("token");
    }
}

export const apiConnection = new ApiConnection();
