export interface User {
  id: string
  username: string
  image: string
  email: string
  passwrod: string
  role: 'user' | 'admin' | 'teacher'
  createdAt: Date
}
export interface AuthState {
  user: User | null
  token: string | null
  loading: boolean
  error: string | null
}
