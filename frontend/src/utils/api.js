// Configuracion de API
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export const api = {
  async get(endpoint) {
    const response = await fetch(${API_BASE_URL})
    return response.json()
  },
  
  async post(endpoint, data) {
    const response = await fetch(${API_BASE_URL}, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    return response.json()
  },
  
  async put(endpoint, data) {
    const response = await fetch(${API_BASE_URL}, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    return response.json()
  },
  
  async delete(endpoint) {
    const response = await fetch(${API_BASE_URL}, {
      method: 'DELETE',
    })
    return response.json()
  },
}
