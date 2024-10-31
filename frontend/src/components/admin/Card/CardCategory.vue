<script setup lang="ts">
import CategoryImgWeb from '@/assets/images/CategoryImgWeb.jpeg'
import { ComputerDesktopIcon, PlusCircleIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHtml5, faJs, faShopify } from '@fortawesome/free-brands-svg-icons';
import type { TListCategories } from '@/interfaces/category.interface';
import { faBullhorn, faC, faCamera, faCameraRetro, faClipboardCheck, faDatabase, faImage, faLaptopCode, faMobileAlt, faPaintBrush, faPhotoFilm, faRectangleAd } from '@fortawesome/free-solid-svg-icons';
import { onMounted, ref } from 'vue';
import ItemsChild from './ItemsChild.vue';
import CardActionButton from './CardActionButton.vue';


const props = defineProps<{
  category: TListCategories; 
}>();

// const listCategories = ref<TListCategories[]>([
//   {
//     image: 'https://maytinhhaiphong.com/wp-content/uploads/2022/10/Cong-viec-Designer.jpg',
//     icon: faPaintBrush,
//     name: 'Graphic Design',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/photoshop.jpg',
//         icon: faImage,
//         name: 'Photoshop',
//         keyword: 'graphic, design, photoshop',
//         description: 'Khóa học Photoshop dành cho người mới bắt đầu.'
//       },
//       {
//         image: 'https://example.com/path/to/adobe-illustrator.jpg',
//         icon: faRectangleAd,
//         name: 'Adobe Illustrator',
//         keyword: 'graphic, design, illustrator',
//         description: 'Khóa học Adobe Illustrator để tạo hình ảnh vector.'
//       },
//       {
//         image: 'https://example.com/path/to/canva.jpg',
//         icon: faC,
//         name: 'Canva',
//         keyword: 'graphic, design, canva',
//         description: 'Khóa học Canva giúp bạn thiết kế đồ họa dễ dàng.'
//       }
//     ]
//   },
//   {
//     image: CategoryImgWeb,
//     icon: faLaptopCode,
//     name: 'Web Development',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/html.jpg',
//         icon: faHtml5,
//         name: 'HTML & CSS',
//         keyword: 'web, development, html, css',
//         description: 'Khóa học HTML & CSS từ cơ bản đến nâng cao.'
//       },
//       {
//         image: 'https://example.com/path/to/javascript.jpg',
//         icon: faJs,
//         name: 'JavaScript',
//         keyword: 'web, development, javascript',
//         description: 'Khóa học JavaScript cho các nhà phát triển web.'
//       }
//     ]
//   },
//   {
//     image: 'https://getflycrm.com/wp-content/uploads/2015/12/digital-marketing.webp',
//     icon: faBullhorn,
//     name: 'Digital Marketing',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/seo.jpg',
//         icon: faShopify,
//         name: 'SEO',
//         keyword: 'marketing, seo',
//         description: 'Khóa học SEO cho doanh nghiệp nhỏ.'
//       },
//       {
//         image: 'https://example.com/path/to/social-media.jpg',
//         icon: faPhotoFilm,
//         name: 'Social Media Marketing',
//         keyword: 'marketing, social media',
//         description: 'Khóa học tiếp thị trên mạng xã hội.'
//       }
//     ]
//   },
//   {
//     image: 'https://www.cdmi.in/courses@2x/data-science.webp',
//     icon: faDatabase,
//     name: 'Data Science',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/python.jpg',
//         icon: faClipboardCheck,
//         name: 'Python for Data Science',
//         keyword: 'data science, python',
//         description: 'Khóa học Python cho phân tích dữ liệu.'
//       },
//       {
//         image: 'https://example.com/path/to/r.jpg',
//         icon: faClipboardCheck,
//         name: 'R Programming',
//         keyword: 'data science, r',
//         description: 'Khóa học R Programming cho phân tích dữ liệu.'
//       }
//     ]
//   },
//   {
//     image: 'https://topdev.vn/blog/wp-content/uploads/2023/02/mobile-app-developer.png',
//     icon: faMobileAlt,
//     name: 'Mobile Development',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/react-native.jpg',
//         icon: faClipboardCheck,
//         name: 'React Native',
//         keyword: 'mobile, react native',
//         description: 'Khóa học React Native để phát triển ứng dụng di động.'
//       },
//       {
//         image: 'https://example.com/path/to/flutter.jpg',
//         icon: faClipboardCheck,
//         name: 'Flutter',
//         keyword: 'mobile, flutter',
//         description: 'Khóa học Flutter để phát triển ứng dụng di động đa nền tảng.'
//       }
//     ]
//   },
//   {
//     image: 'https://sbitzone.com//assets/uploads//course-photo/course-photo-1587657593-sbiz.jpg',
//     icon: faPaintBrush,
//     name: 'Advanced Graphic Design',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/advanced-photoshop.jpg',
//         icon: faImage,
//         name: 'Advanced Photoshop Techniques',
//         keyword: 'graphic, design, photoshop',
//         description: 'Khóa học nâng cao Photoshop với các kỹ thuật mới.'
//       },
//       {
//         image: 'https://example.com/path/to/illustrator-tips.jpg',
//         icon: faRectangleAd,
//         name: 'Illustrator Tips & Tricks',
//         keyword: 'graphic, design, illustrator',
//         description: 'Khóa học mẹo và thủ thuật Illustrator.'
//       }
//     ]
//   },
//   {
//     image: 'https://wiki.matbao.net/wp-content/uploads/2021/10/cyber-security-la-gi-02.jpg',
//     icon: faBullhorn,
//     name: 'Cyber Security',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/network-security.jpg',
//         icon: faClipboardCheck,
//         name: 'Network Security Basics',
//         keyword: 'cyber security, network',
//         description: 'Khóa học cơ bản về bảo mật mạng.'
//       },
//       {
//         image: 'https://example.com/path/to/ethical-hacking.jpg',
//         icon: faClipboardCheck,
//         name: 'Ethical Hacking',
//         keyword: 'cyber security, ethical hacking',
//         description: 'Khóa học hacking mũ trắng và bảo mật thông tin.'
//       }
//     ]
//   },
//   {
//     image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBYXXb9i9n768sHkALYF8iQxH0xyUYMEJXfw&s',
//     icon: faImage,
//     name: 'Photography',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/basics.jpg',
//         icon: faCameraRetro,
//         name: 'Photography Basics',
//         keyword: 'photography, basics',
//         description: 'Khóa học về kỹ thuật chụp ảnh cơ bản.'
//       },
//       {
//         image: 'https://example.com/path/to/advanced-techniques.jpg',
//         icon: faCamera,
//         name: 'Advanced Photography Techniques',
//         keyword: 'photography, advanced',
//         description: 'Khóa học kỹ thuật chụp ảnh nâng cao.'
//       }
//     ]
//   },
//   {
//     image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYeMnnkLWtI6pYS342oBrie7mN09auVclfHQ&s',
//     icon: faClipboardCheck,
//     name: 'English Language Learning',
//     parent_id: [
//       {
//         image: 'https://example.com/path/to/beginner.jpg',
//         icon: faClipboardCheck,
//         name: 'English for Beginners',
//         keyword: 'language, english, beginners',
//         description: 'Khóa học tiếng Anh cho người mới bắt đầu.'
//       },
//       {
//         image: 'https://example.com/path/to/advanced.jpg',
//         icon: faClipboardCheck,
//         name: 'Advanced English',
//         keyword: 'language, english, advanced',
//         description: 'Khóa học tiếng Anh nâng cao.'
//       }
//     ]
//   }
// ])
</script>
<template>
  <div class="bg-white dark:bg-dark-sidebar rounded-lg dark:border group hover:duration-700">
    <div class="p-3 h-full flex flex-wrap justify-center">
      <div class="w-full">
        <img :src="props.category.image" class="w-full h-[170px] object-cover rounded-t-md" alt="">
        <div class="py-4">
          <div class="flex justify-between pb-4 border-b">
            <div class="flex gap-2 items-center">
              <!-- <FontAwesomeIcon :icon="props.category.icon" class="w-5"/> -->
              <div class="font-bold">{{props.category.name}}</div>
            </div>
            <div class="">({{props.category.children?.length}})</div>
          </div>
          <ItemsChild v-for="(CategoryItemChild,index) in props.category.children" :key="index" :child="CategoryItemChild"/>
        </div>
      </div>
      <div class="flex justify-center invisible gap-4 pb-5 group-hover:visible">
        <CardActionButton
        link="#"
        :icon="PlusCircleIcon"
        text="Thêm"
        />
        <CardActionButton
        link="#"
        :icon="PlusCircleIcon"
        text="Sửa"
        />
        <CardActionButton
        link="#"
        :icon="TrashIcon"
        text="Xoá"
        />
      </div>
    </div>
  </div>
</template>
