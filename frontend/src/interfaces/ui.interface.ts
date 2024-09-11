export interface CardService {
  image: string
  heading: string
  description: string
}

export interface Button {
  variant?: 'primary' | 'default'
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
}
