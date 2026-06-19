import {apiConnection} from "@/services/api-connection.js";

class DataManager {
	list()
	{
		return apiConnection.get("events/list").then((response) => response);
	}

	event(id)
	{
		return apiConnection.get(`events/${id}`).then((response) => response);
	}

	updateEvent(id, payload)
	{
		return apiConnection.post(`events/${id}`, payload).then((response) => response);
	}
}

export const dataManager = new DataManager();
