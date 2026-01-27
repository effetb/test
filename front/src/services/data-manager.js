import {apiConnection} from "@/services/api-connection.js";

class DataManager {
	list()
	{
		return apiConnection.get("events/list").then((response) => response);
	}
}

export const dataManager = new DataManager();
