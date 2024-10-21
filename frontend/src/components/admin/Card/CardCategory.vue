<script setup lang="ts">
import CategoryImgWeb from '@/assets/images/CategoryImgWeb.jpeg'
import { ComputerDesktopIcon, PlusCircleIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// import { faLaravel, faPaintBrush, faAdobe, faCanva, faLaptopCode, faHtml5, faJs, faBullhorn, faSearch, faShareAlt } from '@fortawesome/free-brands-svg-icons';
import { faHtml5, faJs, faShopify } from '@fortawesome/free-brands-svg-icons';
import type { ListCategories } from '@/interfaces/admin.interface';
import { faBullhorn, faC, faCamera, faCameraRetro, faClipboardCheck, faDatabase, faImage, faLaptopCode, faMobileAlt, faPaintBrush, faPhotoFilm, faRectangleAd } from '@fortawesome/free-solid-svg-icons';
import { ref } from 'vue';
import ItemsChild from './ItemsChild.vue';
import CardActionButton from './CardActionButton.vue';


const listCategories = ref<ListCategories[]>([
  {
    img: 'https://maytinhhaiphong.com/wp-content/uploads/2022/10/Cong-viec-Designer.jpg',
    icon: faPaintBrush,
    title: 'Graphic Design',
    children: [
      {
        img: 'https://example.com/path/to/photoshop.jpg',
        icon: faImage,
        title: 'Photoshop',
        keyword: 'graphic, design, photoshop',
        description: 'Khóa học Photoshop dành cho người mới bắt đầu.',
      },
      {
        img: 'https://example.com/path/to/adobe-illustrator.jpg',
        icon: faRectangleAd,
        title: 'Adobe Illustrator',
        keyword: 'graphic, design, illustrator',
        description: 'Khóa học Adobe Illustrator để tạo hình ảnh vector.',
      },
      {
        img: 'https://example.com/path/to/canva.jpg',
        icon: faC,
        title: 'Canva',
        keyword: 'graphic, design, canva',
        description: 'Khóa học Canva giúp bạn thiết kế đồ họa dễ dàng.',
      },
    ],
  },
  {
    img: CategoryImgWeb,
    icon: faLaptopCode,
    title: 'Web Development',
    children: [
      {
        img: 'https://example.com/path/to/html.jpg',
        icon: faHtml5,
        title: 'HTML & CSS',
        keyword: 'web, development, html, css',
        description: 'Khóa học HTML & CSS từ cơ bản đến nâng cao.',
      },
      {
        img: 'https://example.com/path/to/javascript.jpg',
        icon: faJs,
        title: 'JavaScript',
        keyword: 'web, development, javascript',
        description: 'Khóa học JavaScript cho các nhà phát triển web.',
      },
    ],
  },
  {
    img: 'https://getflycrm.com/wp-content/uploads/2015/12/digital-marketing.webp',
    icon: faBullhorn,
    title: 'Digital Marketing',
    children: [
      {
        img: 'https://example.com/path/to/seo.jpg',
        icon: faShopify,
        title: 'SEO',
        keyword: 'marketing, seo',
        description: 'Khóa học SEO cho doanh nghiệp nhỏ.',
      },
      {
        img: 'https://example.com/path/to/social-media.jpg',
        icon: faPhotoFilm,
        title: 'Social Media Marketing',
        keyword: 'marketing, social media',
        description: 'Khóa học tiếp thị trên mạng xã hội.',
      },
    ],
  },
  {
    img: 'https://www.cdmi.in/courses@2x/data-science.webp',
    icon: faDatabase,
    title: 'Data Science',
    children: [
      {
        img: 'https://example.com/path/to/python.jpg',
        icon: faClipboardCheck,
        title: 'Python for Data Science',
        keyword: 'data science, python',
        description: 'Khóa học Python cho phân tích dữ liệu.',
      },
      {
        img: 'https://example.com/path/to/r.jpg',
        icon: faClipboardCheck,
        title: 'R Programming',
        keyword: 'data science, r',
        description: 'Khóa học R Programming cho phân tích dữ liệu.',
      },
    ],
  },
  {
    img: 'https://topdev.vn/blog/wp-content/uploads/2023/02/mobile-app-developer.png',
    icon: faMobileAlt,
    title: 'Mobile Development',
    children: [
      {
        img: 'https://example.com/path/to/react-native.jpg',
        icon: faClipboardCheck,
        title: 'React Native',
        keyword: 'mobile, react native',
        description: 'Khóa học React Native để phát triển ứng dụng di động.',
      },
      {
        img: 'https://example.com/path/to/flutter.jpg',
        icon: faClipboardCheck,
        title: 'Flutter',
        keyword: 'mobile, flutter',
        description: 'Khóa học Flutter để phát triển ứng dụng di động đa nền tảng.',
      },
    ],
  },
  {
    img: 'https://sbitzone.com//assets/uploads//course-photo/course-photo-1587657593-sbiz.jpg',
    icon: faPaintBrush,
    title: 'Advanced Graphic Design',
    children: [
      {
        img: 'https://example.com/path/to/advanced-photoshop.jpg',
        icon: faImage,
        title: 'Advanced Photoshop Techniques',
        keyword: 'graphic, design, photoshop',
        description: 'Khóa học nâng cao Photoshop với các kỹ thuật mới.',
      },
      {
        img: 'https://example.com/path/to/illustrator-tips.jpg',
        icon: faRectangleAd,
        title: 'Illustrator Tips & Tricks',
        keyword: 'graphic, design, illustrator',
        description: 'Khóa học mẹo và thủ thuật Illustrator.',
      },
    ],
  },
  {
    img: 'https://wiki.matbao.net/wp-content/uploads/2021/10/cyber-security-la-gi-02.jpg',
    icon: faBullhorn,
    title: 'Cyber Security',
    children: [
      {
        img: 'https://example.com/path/to/network-security.jpg',
        icon: faClipboardCheck,
        title: 'Network Security Basics',
        keyword: 'cyber security, network',
        description: 'Khóa học cơ bản về bảo mật mạng.',
      },
      {
        img: 'https://example.com/path/to/ethical-hacking.jpg',
        icon: faClipboardCheck,
        title: 'Ethical Hacking',
        keyword: 'cyber security, ethical hacking',
        description: 'Khóa học hacking mũ trắng và bảo mật thông tin.',
      },
    ],
  },
  {
    img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBYXXb9i9n768sHkALYF8iQxH0xyUYMEJXfw&s',
    icon: faImage,
    title: 'Photography',
    children: [
      {
        img: 'https://example.com/path/to/basics.jpg',
        icon: faCameraRetro,
        title: 'Photography Basics',
        keyword: 'photography, basics',
        description: 'Khóa học về kỹ thuật chụp ảnh cơ bản.',
      },
      {
        img: 'https://example.com/path/to/advanced-techniques.jpg',
        icon: faCamera,
        title: 'Advanced Photography Techniques',
        keyword: 'photography, advanced',
        description: 'Khóa học kỹ thuật chụp ảnh nâng cao.',
      },
    ],
  },
  {
    img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYeMnnkLWtI6pYS342oBrie7mN09auVclfHQ&s',
    icon: faClipboardCheck,
    title: 'English Language Learning',
    children: [
      {
        img: 'https://example.com/path/to/beginner.jpg',
        icon: faClipboardCheck,
        title: 'English for Beginners',
        keyword: 'language, english, beginners',
        description: 'Khóa học tiếng Anh cho người mới bắt đầu.',
      },
      {
        img: 'https://example.com/path/to/advanced.jpg',
        icon: faClipboardCheck,
        title: 'Advanced English',
        keyword: 'language, english, advanced',
        description: 'Khóa học tiếng Anh nâng cao.',
      },
    ],
  },
]) 

</script>
<template>
  <div v-for="(CartItem,index) in listCategories" :key="index" class="bg-white dark:bg-dark-sidebar rounded-lg dark:border group hover:duration-700">
    <div class="p-3">
      <img :src="CartItem.img" class="w-full h-[170px] object-cover rounded-t-md" alt="">
      <div class="py-4">
        <div class="flex justify-between pb-4 border-b">
          <div class="flex gap-2 items-center">
            <FontAwesomeIcon :icon="CartItem.icon" class="w-5"/>
            <div class="font-bold">{{CartItem.title}}</div>
          </div>
          <div class="">({{CartItem.children?.length}})</div>
        </div>
        <ItemsChild v-for="(CartItemChild,index) in CartItem.children" :key="index" :child="CartItemChild"/>
      </div>
      <div class="flex justify-center gap-4 pb-5 hidden group-hover:flex">
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
