// stores/useHomeStore.ts
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '@/services/axiosConfig'
import type { Tlevel } from '@/interfaces/level.interface'
import type { TCardCourse } from '@/interfaces/course.interface'
import type { TCategory } from '@/interfaces/category.interface'

export const apisStore = defineStore('homeStore', () => {
  // State
  const categories = ref<TCategory[]>([])
  const courses = ref<TCardCourse[]>([])
  const levels = ref<Tlevel[]>([])
  const languagies = ref<Tlevel[]>([])

  // Actions for fetching different data
  const fetchCate = async () => {
    try {
      const res = await api.get('/categories')
      categories.value = res.data.data.data
      console.log(categories.value);
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }
  const fetchLevel = async () => {
    try {
      const res = await api.get('/course-levels')
      levels.value = res.data.data.data
    } catch (error) {
      console.error('Error fetching course-levels:', error)
    }
  }
  const fetchLang = async () => {
    try {
      const res = await api.get('/languages')
      languagies.value = res.data.data.data
    } catch (error) {
      console.error('Error fetching course-levels:', error)
    }
  }

  const fetchCourse = async () => {
    try {
      const res = await api.get('/courses')
      courses.value = res.data.data.data
    } catch (error) {
      console.error('Error fetching courses:', error)
    }
  }
  const getCourses = computed(() => {
    return courses.value.filter((course) => course.status === 'active')
  })

  const categoriesWithChildren = computed(() => {
    return categories.value.filter((category) => category.children && category.children.length > 0)
  })
  const categoriesWithoutChildren = computed(() => {
    return categories.value.filter(
      (category) => !category.children || category.children.length === 0
    )
  })
  return {
    languagies,
    categories,
    courses,
    levels,
    fetchCate,
    fetchCourse,
    fetchLevel,
    fetchLang,
    getCourses,
    categoriesWithChildren,
    categoriesWithoutChildren
  }
})
