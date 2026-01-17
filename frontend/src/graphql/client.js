import { GraphQLClient } from 'graphql-request'

// URL de tu backend GraphQL - usa el valor del .env o el default
const GRAPHQL_ENDPOINT = import.meta.env.VITE_GRAPHQL_URL || 'http://localhost:8000/graphql'

console.log('GraphQL Endpoint:', GRAPHQL_ENDPOINT)

// Crea el cliente GraphQL
export const graphqlClient = new GraphQLClient(GRAPHQL_ENDPOINT, {
  headers: {
    'Content-Type': 'application/json',
  },
})

// Función para ejecutar queries
export async function executeQuery(query, variables = {}) {
  try {
    console.log('Ejecutando query:', { query, variables })
    const data = await graphqlClient.request(query, variables)
    console.log('Query response:', data)
    return data
  } catch (error) {
    console.error('GraphQL Error:', error)
    throw error
  }
}

// Función para ejecutar mutations
export async function executeMutation(mutation, variables = {}) {
  try {
    console.log('Ejecutando mutation:', { mutation, variables })
    const data = await graphqlClient.request(mutation, variables)
    console.log('Mutation response:', data)
    return data
  } catch (error) {
    console.error('GraphQL Mutation Error:', error)
    throw error
  }
}