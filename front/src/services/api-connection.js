import axios from "axios";

class ApiConnection {
	get(path)
	{
		return this.fetchApi("GET", path);
	}

	post(path, params = {})
	{
		return this.fetchApi("POST", path, params);
	}

	async fetchApi(method, path, params = {}, secured = true)
	{
		if (!this.token() && secured)
		{
			await this.login();
		}

		const url   = import.meta.env.VITE_API_URL + "/" + path;
		let headers = {};

		if (params)
		{
			headers = {
				"Content-Type": "application/json;charset=utf-8",
			};
		}

		if (secured && this.token())
		{
			headers["Authorization"] = "Bearer " + this.token();
		}

		const call = axios({
			method : method,
			url    : url,
			data   : params,
			headers: headers,
			origin : "*",
		});

		return new Promise((resolve, reject) =>
		{
			call
				.then((response) =>
				{
					resolve(response.data);
				})
				.catch((error) =>
				{
					const status = error?.response?.status;
					if (status === 401 || status === 403)
					{
						this.clearToken();
						window.location.reload();
						return;
					}

					reject(error?.response?.data ?? error);
				});
		});
	}

	login()
	{
		const credentials = {
			username: import.meta.env.VITE_API_USERNAME,
			password: import.meta.env.VITE_API_PWD,
		};
		return new Promise((resolve) =>
		{
			apiConnection
				.fetchApi("post", "login_check", credentials, false)
				.then((data) =>
				{
					if (data.token)
					{
						window.localStorage.setItem("token", data.token);
						resolve(data);
					}
				})
				.catch((res) =>
				{
					resolve(res);
				});
		});
	}

	token()
	{
		return window.localStorage.getItem("token");
	}

	clearToken()
	{
		window.localStorage.removeItem("token");
	}
}

export const apiConnection = new ApiConnection();
