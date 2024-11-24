export interface TLanguage {
  id?: number
  name: string
  description: string
  status: 'active' | 'inactive'
}
export interface LanguageState {
  languages: TLanguage[]
  deletedLanguages: TLanguage[]
  error: string | null
  total: number
}
