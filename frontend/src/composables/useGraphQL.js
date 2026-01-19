import { executeQuery, executeMutation } from '@/graphql/client'
import { ref } from 'vue'

export function useGraphQL() {
  const loading = ref(false)
  const error = ref(null)

  const query = async (queryString, variables = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const data = await executeQuery(queryString, variables)
      return data
    } catch (err) {
      error.value = err
      console.error('Error en query GraphQL:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const mutation = async (mutationString, variables = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const data = await executeMutation(mutationString, variables)
      return data
    } catch (err) {
      error.value = err
      console.error('Error en mutation GraphQL:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    query,
    mutation
  }
}