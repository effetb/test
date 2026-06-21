import { apiConnection } from "@/services/api-connection.js";

class DataManager {
    list() {
        return apiConnection.get("/api/events/list").then((response) => response.data);
    }
}

export const dataManager = new DataManager();
