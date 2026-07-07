import {apiConnection} from "@/services/api-connection.js";

class DataManager {
	list()
	{
		return apiConnection.get("events/list").then((response) => response);
	}
	//added to consume rest api get event detail
	detail(id)
	{
		return apiConnection.get(`events/${id}`).then((response) => response);
	}

	update(id, data)
	{
		return apiConnection.post(`events/${id}`, data).then((response) => response);
	}
}

export const dataManager = new DataManager();
