import { useCourseStore } from '@/store/course'
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useCourseDetail() {
  const router = useRouter()
  const route = useRoute()
  const navigateToDetail = (id: number) => {
    router.push({ name: 'user.course.detail', params: { id } })
  }
  const courseStore = useCourseStore()
  onMounted(() => {
    const courseId = route.params.id as string
    courseStore.fetchCourseDetail(courseId)
  })
  return {
    courseStore,
    navigateToDetail
  }
}
