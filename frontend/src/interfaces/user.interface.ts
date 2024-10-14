export interface User {
  id: string
  username: string
  image: string
  email: string
  passwrod: string
  role: 'student' | 'admin' | 'teacher'
  createdAt: Date
}
export interface AuthState {
  user: User | null
  token: string | null
  loading: boolean
  error: string | null
}

export interface CourseDetailUser {
  image?: string
  name: string
  job: string
  rate: string | number
  review: string | number
  students: string | number
  course: string | number
  introduce: string
}
