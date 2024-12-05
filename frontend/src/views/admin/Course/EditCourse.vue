<template>
  <div>
    <div class="p-4">
      <HeaderNavbar :namePage="formDataEditCourse.title">
        <ButtonSecondary :icon="ArrowLeftIcon" link="#" title="Trước" />
        <ButtonSecondary :icon="ArrowTopRightOnSquareIcon" link="#" title="Hỗ trợ" />
      </HeaderNavbar>
    </div>
    <div class="p-4 py-2">
      <div class="background-table">
        <div class="p-3">
          <form @submit.prevent="submitFormEdit">
            <div class="flex justify-between gap-2 lg:flex-row flex-col">
              <div class="flex gap-5">
                <ButtonSecondary
                  :icon="ArrowTopRightOnSquareIcon"
                  link="#"
                  title="Xem trước giao diện"
                  customStyle="!flex-row-reverse font-normal"
                />
                <ButtonSecondary
                  :icon="ArrowTopRightOnSquareIcon"
                  link="#"
                  title="Trình phát khoá học"
                  customStyle="!flex-row-reverse font-normal"
                />
              </div>
              <div class="flex">
                <SubmitButtonPrimary title="Lưu thay đổi" />
              </div>
            </div>
            <div class="pt-4 pb-8">
              <el-tabs
                :tab-position="tabPosition"
                style="height: 200px"
                class="demo-tabs gap-3 !h-auto md:flex-row-reverse flex-col-reverse"
              >
                <el-tab-pane>
                  <template #label>
                    <span class="tab-items-style">
                      <PencilSquareIcon class="w-5 font-bold" />
                      <span>Chương trình giảng dạy</span>
                    </span>
                  </template>
                  <div class="px-2">
                    <div class="my-2 flex gap-2">
                      <!-- button dialog 1 new section -->
                      <ButtonPrimarySm
                        title="Thêm chương"
                        dialogVisible="dialogAddnewSection"
                        link="#"
                        @click="addChapter"
                      />
                      <!-- button dialog 2 new lesson -->
                      <template v-if="section.length > 0">
                        <ButtonPrimarySm
                          title="Thêm bài học"
                          dialogVisible="dialogAddnewLecture"
                          link="#"
                          @click="addLesson"
                        />
                        <!-- button dialog 3 new quiz -->
                        <ButtonPrimarySm
                          title="Thêm quiz"
                          dialogVisible="dialogAddnewQuiz"
                          link="#"
                          @click="addQuiz"
                        />
                        <!-- button dialog 4 sort section -->
                        <ButtonPrimarySm
                          title="Sắp xếp chương"
                          dialogVisible="dialogSortSection"
                          link="#"
                          @click="sortSection"
                        />
                      </template>
                    </div>
                    <template v-if="section.length == 0">
                      <div class="flex flex-row mt-6">
                        <div class="mt-4 basis-8/12">
                          <ButtonAddObject @click="addChapter" title="Thêm chương mới" />
                        </div>
                      </div>
                    </template>

                    <template v-else>
                      <!-- Dropdown items -->
                      <div class="mt-6">
                        <template v-for="(itemSection, index) in section" :key="itemSection.id">
                          <el-collapse class="mb-2" @change="ActiveToggle">
                            <el-collapse-item
                              name="1"
                              class="group style-input hover:opacity-100"
                              @click.stop
                            >
                              <template #title>
                                {{ index + 1 }}. {{ itemSection.title }}
                                <div
                                  class="gap-2 el-collapse-item__btn flex transition-opacity duration-200 group-hover:opacity-100 opacity-0"
                                  :class="{
                                    'opacity-100': toggleActive,
                                    'opacity-0': !toggleActive
                                  }"
                                >
                                  <ButtonSecondarySm
                                    v-if="
                                      itemSection.lectures &&
                                      Array.isArray(itemSection.lectures) &&
                                      itemSection.lectures.length > 0
                                    "
                                    title="Sắp xếp bài học"
                                  />
                                  <ButtonSecondarySm
                                    link="#"
                                    :icon="PencilIcon"
                                    @click.prevent="
                                      itemSection.id !== undefined && EditChapter(itemSection.id)
                                    "
                                  />
                                  <ButtonSecondarySm
                                    link="#"
                                    :icon="TrashIcon"
                                    @click.prevent="
                                      itemSection.id !== undefined &&
                                      handleDeleteSection(itemSection.id)
                                    "
                                  />
                                </div>
                              </template>
                              <template
                                v-if="
                                  itemSection.lectures &&
                                  Array.isArray(itemSection.lectures) &&
                                  itemSection.lectures.length > 0
                                "
                              >
                                <template
                                  v-for="itemLecture in itemSection.lectures"
                                  :key="itemLecture.id"
                                >
                                  <div
                                    class="py-2 border-b-2 last:border-none flex justify-between"
                                  >
                                    {{ itemLecture.title }}
                                    <div class="flex gap-2">
                                      {{ itemLecture.type }}
                                      <ButtonSecondarySm
                                        :icon="PencilIcon"
                                        link="#"
                                        @click.prevent="
                                          itemLecture.id !== undefined && editLesson(itemLecture.id)
                                        "
                                      />
                                      <ButtonSecondarySm
                                        link="#"
                                        :icon="TrashIcon"
                                        @click.prevent="
                                          itemLecture.id !== undefined &&
                                          handleDeleteLecture(itemLecture.id)
                                        "
                                      />
                                    </div>
                                  </div>
                                </template>
                              </template>
                              <template
                                v-else-if="
                                  itemSection.quizzes &&
                                  Array.isArray(itemSection.quizzes) &&
                                  itemSection.quizzes.length > 0
                                "
                              >
                                <template
                                  v-for="itemQuizzes in itemSection.quizzes"
                                  :key="itemQuizzes.id"
                                >
                                  <div
                                    class="py-2 border-b-2 last:border-none flex justify-between"
                                  >
                                    {{ itemQuizzes.title }}
                                    <div class="flex gap-2">
                                      Quizz
                                      <ButtonSecondarySm
                                        :icon="QueueListIcon"
                                        link="#"
                                        @click.prevent="
                                          itemQuizzes.id !== undefined && handelQues(itemQuizzes.id)
                                        "
                                      />
                                      <ButtonSecondarySm
                                        :icon="PencilIcon"
                                        link="#"
                                        @click.prevent="
                                          itemQuizzes.id !== undefined && EditQuiz(itemQuizzes.id)
                                        "
                                      />
                                      <ButtonSecondarySm
                                        link="#"
                                        :icon="TrashIcon"
                                        @click.prevent="
                                          itemQuizzes.id !== undefined &&
                                          handleDeleteQuiz(itemQuizzes.id)
                                        "
                                      />
                                    </div>
                                  </div>
                                </template>
                              </template>
                              <template v-else>
                                <div class="py-2 border-b-2 last:border-none">
                                  Không có bài học nào
                                </div>
                              </template>
                            </el-collapse-item>
                          </el-collapse>
                        </template>
                      </div>
                    </template>
                  </div>
                </el-tab-pane>

                <el-tab-pane>
                  <template #label>
                    <span class="tab-items-style">
                      <DocumentDuplicateIcon class="w-5 font-bold" />
                      <span>Nền tảng</span>
                    </span>
                  </template>
                  <div class="px-2">
                    <InputGroup
                      label="Tiêu đề"
                      required="*"
                      inputId="name"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      v-model="formDataEditCourse.title"
                    />

                    <DescriptionGroup
                      label="Mô tả ngắn"
                      inputId="description"
                      inputPlaceHoder="Nhập mô tả..."
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      v-model="formDataEditCourse.short_description"
                    />
                    <CkeditorGroup
                      label="Nội dung"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      v-model="formDataEditCourse.description"
                    />

                    <SelectGroup
                      inputPlaceHoder="Chọn danh mục cho khoá học"
                      required="*"
                      label="Danh mục"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      v-model="formDataEditCourse.category_id"
                      :optionsData="categories"
                    />
                    <SelectGroup
                      inputPlaceHoder="Chọn cấp độ cho khoá học"
                      required="*"
                      label="Cấp độ"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      :optionsData="courseLevels"
                      v-model="formDataEditCourse.level_id"
                    />
                    <SelectGroup
                      inputPlaceHoder="Chọn ngôn ngữ cho khoá học"
                      required="*"
                      label="Ngôn ngữ"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      :optionsData="languages"
                      v-model="formDataEditCourse.language_id"
                    />
                    <RadioGroup
                      label="Chọn"
                      required="*"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                    >
                      <div class="mb-2 ml-4">
                        <el-radio-group class="grid" v-model="formDataEditCourse.status">
                          <el-radio value="active">Kích hoạt</el-radio>
                          <el-radio value="inactive">Không kích hoạt</el-radio>
                        </el-radio-group>
                      </div>
                    </RadioGroup>
                  </div>
                </el-tab-pane>

                <el-tab-pane>
                  <template #label>
                    <span class="tab-items-style">
                      <BanknotesIcon class="w-5 font-bold" />
                      <span>Loại giá</span>
                    </span>
                  </template>
                  <div class="px-2">
                    <RadioGroup
                      label="Loại giá"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                    >
                      <el-radio-group class="grid" v-model="paid">
                        <el-radio value="1">Có phí</el-radio>
                        <el-radio value="0">Miễn phí</el-radio>
                      </el-radio-group>
                    </RadioGroup>
                    <template
                      :class="{ block: paid == 1, hidden: paid == 0 }"
                      class="transition-opacity translate-y-2 duration-500 ease-in-out"
                    >
                      <InputGroup
                        label="Giá (đơn vị vnđ)"
                        required="*"
                        inputPlaceHoder="Nhập giá của khoá học"
                        customsClass="flex flex-row items-center"
                        customsClassChild="basis-2/12"
                        customsClassChild2="basis-10/12 w-full"
                        v-model="formDataEditCourse.price"
                      />
                      <div v-show="checked2" class="relative">
                        <el-tooltip
                          class="box-item"
                          effect="dark"
                          content="Chuyển sang phần trăm"
                          placement="top"
                        >
                          <ArrowPathIcon
                            class="w-5 absolute top-0 left-[100px] bold color-primary"
                          />
                        </el-tooltip>
                        <InputGroup
                          label="Giá giảm"
                          required="*"
                          inputPlaceHoder="Nhập giá giảm của khoá học"
                          customsClass="flex"
                          customsClassChild="basis-2/12"
                          customsClassChild2="basis-10/12 w-full"
                          v-model="formDataEditCourse.sale_value"
                        />
                      </div>
                      <div class="mt-1 flex justify-end">
                        <el-checkbox
                          class="basis-10/12"
                          v-model="checked2"
                          label="Click nếu khoá học này có sử dụng mã giảm giá"
                          size="large"
                        />
                      </div>
                    </template>
                  </div>
                </el-tab-pane>

                <el-tab-pane>
                  <template #label>
                    <span class="tab-items-style">
                      <LinkIcon class="w-5 font-bold" />
                      <span>Phương tiện truyền thông</span>
                    </span>
                  </template>
                  <div class="px-2">
                    <UploadGroup
                      label="Hình ảnh thumbnail"
                      inputPlaceHoder="Nhập giá của khoá học đã giảm"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      :handlePreviewImg="handlePreviewImg"
                    />
                    <UploadGroup
                      label="Hình ảnh banner"
                      inputPlaceHoder="Nhập giá của khoá học đã giảm"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                    />
                    <div class="border mt-7 mb-5"></div>

                    <RadioGroup
                      label="Video intro"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                    >
                      <el-radio-group class="flex" v-model="linkVideo">
                        <el-radio value="1">Đường dẫn video</el-radio>
                        <el-radio value="0">Tải lên file video</el-radio>
                      </el-radio-group>
                    </RadioGroup>
                    <UploadGroup
                      label="Video file"
                      required="*"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      :class="{ hidden: linkVideo == 1 }"
                    />
                    <InputGroup
                      label="Video link"
                      inputId="video"
                      required="*"
                      inputPlaceHoder="Nhập đường dẫn video"
                      customsClass="flex flex-row items-center"
                      customsClassChild="basis-2/12"
                      customsClassChild2="basis-10/12 w-full"
                      :class="{ hidden: linkVideo == 0 }"
                    />
                  </div>
                </el-tab-pane>
              </el-tabs>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Dialog thêm chương -->
  <DialogArea
    :dialogVisible="dialogAddnewSection"
    title="Thêm chương mới"
    @close="dialogAddnewSection = false"
    :submitForm="handelFormSection"
  >
    <InputGroup
      label="Tên chương"
      inputId="nameSection"
      inputPlaceHoder="Nhập tên chương"
      v-model="formDataAddSection.title"
    />
  </DialogArea>
  <!-- End Dialog thêm chương -->
  <!-- Dialog Chỉnh sửa chương -->
  <DialogArea
    :dialogVisible="dialogEditSection"
    title="Chỉnh sửa chương"
    @close="dialogEditSection = false"
    :submitForm="() => handleEditSection(selectedSectionId)"
  >
    <InputGroup
      label="Chỉnh sửa tên chương"
      inputId="nameEditSection"
      inputPlaceHoder="Nhập tên chương"
      v-model="formDataEditSection.title"
    />

  </DialogArea>
  <!-- End Dialog Chỉnh sửa chương -->

  <!-- Dialog thêm bài học -->
  <DialogArea
    :dialogVisible="dialogAddnewLecture"
    title="Thêm bài học mới"
    @close="dialogAddnewLecture = false"
    :submitForm="() => handleAddLecture()"
  >
    <label class="label-input mt-2 mb-3 flex"> Chọn thể loại bài học</label>
    <el-radio-group class="w-full" v-model="formDataAddLecture.type">
      <el-radio value="video" size="large" border>Video file</el-radio>
      <el-radio value="file" size="large " border>Tài liệu</el-radio>
    </el-radio-group>
    <InputGroup
      label="Tên bài học"
      inputId="nameLecture"
      inputPlaceHoder="Nhập tên bài học"
      v-model="formDataAddLecture.title"
    />
    <RadioGroup label="Chương" customsClass="items-center">
      <el-select
        v-model="formDataAddLecture.section_id"
        placeholder="Chọn chương cho bài học"
        class="w-full"
        filterable
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option v-for="item in section" :key="item.id" :label="item.title" :value="item.id">
          <span>{{ item.title }}</span>
        </el-option>
      </el-select>
    </RadioGroup>
    <RadioGroup
      v-if="formDataAddLecture.type === 'video'"
      label="Tải lên video bài học (.mp4)"
      customsClass="items-center"
    >
      <el-upload
        class="upload-demo"
        drag
        multiple
        :before-upload="(file: File) => handleFileUpload(file, 'content')"
      >
        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
        <div class="el-upload__text">Kéo file thả vào đây hoặc <em>click để tải lên</em></div>
        <template #tip>
          <div class="el-upload__tip">các tệp .mp4 có kích thước nhỏ hơn 20mb</div>
        </template>
      </el-upload>
    </RadioGroup>
    <RadioGroup
      v-if="formDataAddLecture.type === 'file'"
      label="Tải file tài liệu bài học (.pdf)"
      customsClass="items-center"
    >
      <el-upload
        class="upload-demo"
        drag
        :before-upload="(file: File) => handleFileUpload(file, 'content')"
        multiple
      >
        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
        <div class="el-upload__text">Kéo file thả vào đây hoặc <em>click để tải lên</em></div>
        <template #tip>
          <div class="el-upload__tip">các tệp pdf có kích thước nhỏ hơn 20mb</div>
        </template>
      </el-upload>
    </RadioGroup>

    <RadioGroup
      :label="formDataAddLecture.type == 'video' ? 'Thời lượng video' : 'Số trang'"
      customsClass="items-center"
    >
      <el-input
        v-model="formDataAddLecture.duration"
        class=""
        disabled
        placeholder="Please input"
      />
    </RadioGroup>

    <RadioGroup
      :label="
        formDataAddLecture.type == 'video' ? 'Chọn xem trước video' : 'Chọn xem trước tài liệu'
      "
      customsClass="items-center"
    >
      <el-select
        v-model="formDataAddLecture.preview"
        class="w-full"
        filterable
        placeholder="Chọn loại xem trước"
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option label="Xem trước" value="can"></el-option>
        <el-option label="Không xem trước" value="cant"></el-option>
      </el-select>
    </RadioGroup>
  </DialogArea>
  <!-- end Dialog thêm bài học -->
  <!-- Dialog chỉnh sửa bài học -->
  <DialogArea
    :dialogVisible="dialogEditLecture"
    title="Chỉnh sửa bài học"
    @close="dialogEditLecture = false"
    :submitForm="() => handleEditLecture(selectedLectureId)"
  >
    <label class="label-input mt-2 mb-3 flex"> Chọn thể loại bài học</label>
    <el-radio-group class="w-full" v-model="formDataEditLecture.type">
      <el-radio value="video" size="large" border>Video file</el-radio>
      <el-radio value="file" size="large " border>Tài liệu</el-radio>
    </el-radio-group>
    <InputGroup
      label="Tên bài học"
      inputId="nameLecture"
      inputPlaceHoder="Nhập tên bài học"
      v-model="formDataEditLecture.title"
    />
    <RadioGroup label="Chương" customsClass="items-center">
      <el-select
        v-model="formDataEditLecture.section_id"
        placeholder="Chọn chương cho bài học"
        class="w-full"
        filterable
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option v-for="item in section" :key="item.id" :label="item.title" :value="item.id">
          <span>{{ item.title }}</span>
        </el-option>
      </el-select>
    </RadioGroup>
    <RadioGroup
      v-if="formDataEditLecture.type === 'video'"
      label="Tải lên video bài học (.mp4)"
      customsClass="items-center"
    >
      <el-upload
        class="upload-demo"
        drag
        multiple
        :before-upload="(file: File) => handleFileUpload(file, 'content')"
      >
        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
        <div class="el-upload__text">Kéo file thả vào đây hoặc <em>click để tải lên</em></div>
        <template #tip>
          <div class="el-upload__tip">các tệp .mp4 có kích thước nhỏ hơn 20mb</div>
        </template>
      </el-upload>
    </RadioGroup>
    <RadioGroup
      v-if="formDataEditLecture.type === 'file'"
      label="Tải file tài liệu bài học (.pdf)"
      customsClass="items-center"
    >
      <el-upload
        class="upload-demo"
        drag
        :before-upload="(file: File) => handleFileUpload(file, 'content')"
        multiple
      >
        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
        <div class="el-upload__text">Kéo file thả vào đây hoặc <em>click để tải lên</em></div>
        <template #tip>
          <div class="el-upload__tip">các tệp pdf có kích thước nhỏ hơn 20mb</div>
        </template>
      </el-upload>
    </RadioGroup>

    <RadioGroup
      :label="formDataEditLecture.type == 'video' ? 'Thời lượng video' : 'Số trang'"
      customsClass="items-center"
    >
      <el-input
        v-model="formDataEditLecture.duration"
        class=""
        disabled
        placeholder="Please input"
      />
    </RadioGroup>

    <RadioGroup
      :label="
        formDataEditLecture.type == 'video' ? 'Chọn xem trước video' : 'Chọn xem trước tài liệu'
      "
      customsClass="items-center"
    >
      <el-select
        v-model="formDataEditLecture.preview"
        class="w-full"
        filterable
        placeholder="Chọn loại xem trước"
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option label="Xem trước" value="can"></el-option>
        <el-option label="Không xem trước" value="cant"></el-option>
      </el-select>
    </RadioGroup>
  </DialogArea>
  <!-- end Dialog chỉnh sửa bài học -->

  <!-- <DialogArea
    :dialogVisible="dialogAddnewLesson"
    title="Thêm bài học mới"
    @close="dialogAddnewLesson = false"
  >
    <InputGroup
      label="Tên bài học"
      inputId="nameLecture"
      inputPlaceHoder="Nhập tên bài học"
      v-model="formDataAddLecture.title"
    />
    <CkeditorGroup label="Nội dung bài học" />
    <InputGroup inputId="duration" label="Thời lượng" inputPlaceHoder="Nhập thời lượng bài học" />
    <InputGroup
      inputId="videoLesson"
      label="Video bài học"
      inputPlaceHoder="Nhập đường dẫn video"
    />
  </DialogArea> -->
  <!-- QUIZZES -->
  <!-- Dialog thêm quiz -->
  <DialogArea
    :dialogVisible="dialogAddnewQuiz"
    title="Thêm quizz mới"
    @close="dialogAddnewQuiz = false"
    :submitForm="handelFormQuiz"
  >
    <RadioGroup label="Chương" customsClass="items-center">
      <el-select
        v-model="formDataAddQuiz.section_id"
        placeholder="Chọn chương cho quizz"
        class="w-full"
        filterable
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option v-for="item in section" :key="item.id" :label="item.title" :value="item.id">
          <span>{{ item.title }}</span>
        </el-option>
      </el-select>
    </RadioGroup>
    <InputGroup
      inputId="nameQuiz"
      label="Tên quiz"
      inputPlaceHoder="Nhập tên quiz"
      v-model="formDataAddQuiz.title"
    />
  </DialogArea>
  <!-- End Dialog chỉnh sửa quiz -->
  <DialogArea
    :dialogVisible="dialogEditQuiz"
    title="Chỉnh sửa quizz"
    @close="dialogEditQuiz = false"
    :submitForm="() => handleEditQuiz(selectedQuizId)"
  >
    <RadioGroup label="Chương" customsClass="items-center">
      <el-select
        v-model="formDataEditQuiz.section_id"
        placeholder="Chọn chương cho quizz"
        class="w-full"
        filterable
      >
        <template #empty>
          <span>Không có giá trị nào</span>
        </template>
        <el-option v-for="item in section" :key="item.id" :label="item.title" :value="item.id">
          <span>{{ item.title }}</span>
        </el-option>
      </el-select>
    </RadioGroup>
    <InputGroup
      inputId="nameQuiz"
      label="Tên quiz"
      inputPlaceHoder="Nhập tên quiz"
      v-model="formDataEditQuiz.title"
    />
  </DialogArea>
  <!-- End Dialog chỉnh sửa quiz -->
  <!--Dialog question -->
  <DialogArea 
  :dialogVisible="dialogQues" 
  title="Câu hỏi" 
  @close="dialogQues = false">
    <div class="py-2 flex gap-2">
      <ButtonPrimarySm
        title="Thêm câu hỏi"
        dialogVisible="dialogAddnewQues"
        link="#"
        @click="addQues"
      />
      <template v-if="question.length > 0">
        <ButtonPrimarySm
          title="Sắp xếp câu hỏi"
          dialogVisible="dialogSortSection"
          link="#"
          @click="sortSection"
        />
      </template>
    </div>
    <template v-if="question.length == 0">
      <div class="flex flex-row mt-6 justify-center">
        <div class="mt-4 basis-8/12">
          <ButtonAddObject @click="addQues" title="Thêm câu hỏi" />
        </div>
      </div>
    </template>
    <template v-else>
      <template v-for="itemQues in question" :key="itemQues.id">
        <div class="py-2 border-b-2 last:border-none flex justify-between">
          {{ itemQues.question }}
          <div class="flex gap-2">
            {{ itemQues.type }}
            <ButtonSecondarySm
              :icon="PencilIcon"
              link="#"
              @click.prevent="itemQues.id !== undefined && editLesson(itemQues.id)"
            />
            <ButtonSecondarySm
              link="#"
              :icon="TrashIcon"
              @click.prevent="itemQues.id !== undefined && handleDeleteQues(itemQues.id)"
            />
          </div>
        </div>
      </template>
    </template>
  </DialogArea>
  <!-- End Dialog question -->
  <!-- End Dialog thêmm question-->
  <DialogArea
    :dialogVisible="dialogAddnewQues"
    title="Thêm câu hỏi"
    @close="dialogEditQuiz = false"
    :submitForm="() => handleEditQuiz(selectedQuizId)"
  >
    <CkeditorGroup
      label="Câu hỏi"
    />
    <RadioGroup
    label="Đáp án"
    required="*"
    customsClass="items-center"
  
    >
    
    </RadioGroup>
    <RadioGroup
      label="Đáp án đúng"
      required="*"
      customsClass="items-center"
    
    >
      
    </RadioGroup>
  </DialogArea>
  
  <!-- End Dialog chỉnh sửa quiz -->
  <!-- END QUIZZES -->

  <DialogArea
    :dialogVisible="dialogSortSection"
    title="Sắp xếp các chương"
    @close="dialogSortSection = false"
    :submitForm="() => handleSortSection(section)"
  >
    <draggable v-model="section" @start="onDragStart" @end="onDragEnd" item-key="id">
      <template #item="{ element }">
        <div>
          <el-input style="max-width: 600px" class="cursor-move py-2" :placeholder="element.title">
            <template #title>{{ element.name }}</template>
            <template #append>
              <ArrowsUpDownIcon class="w-5 cursor-move" />
            </template>
          </el-input>
        </div>
      </template>
    </draggable>
    <rawDisplayer class="col-3" :value="section" title="List" />
  </DialogArea>
  <!-- End Dialog Sắp xếp các chương -->
</template>
<script setup lang="ts">
import { UploadFilled } from '@element-plus/icons-vue'
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue'
import ButtonPrimarySm from '@/components/admin/Button/ButtonPrimarySm.vue'
import ButtonSecondary from '@/components/admin/Button/ButtonSecondary.vue'
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import { useSidebarStore } from '@/store/sidebar'
import draggable from 'vuedraggable';


import {
  ArrowLeftIcon,
  ArrowTopRightOnSquareIcon,
  BanknotesIcon,
  DocumentDuplicateIcon,
  InformationCircleIcon,
  LinkIcon,
  PencilIcon,
  PencilSquareIcon,
  PlusIcon,
  QueueListIcon,
  TagIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import InputGroup from '@/components/admin/Dialog/InputGroup.vue'
import DescriptionGroup from '@/components/admin/Dialog/DescriptionGroup.vue'
import CkeditorGroup from '@/components/admin/Dialog/CkeditorGroup.vue'
import SelectGroup from '@/components/admin/Dialog/SelectGroup.vue'
import RadioGroup from '@/components/admin/Dialog/RadioGroup.vue'
import UploadGroup from '@/components/admin/Dialog/UploadGroup.vue'
import InputItems from '@/components/admin/Dialog/InputItems.vue'
import { ArrowPathIcon, ArrowsUpDownIcon, MinusIcon } from '@heroicons/vue/24/solid'
import ButtonAddObject from '@/components/admin/Button/ButtonAddObject.vue'
import InputOptionGroup from '@/components/admin/Dialog/InputOptionGroup.vue'
import DropdownItems from '@/components/admin/Dialog/DropdownItems.vue'
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue'
import DialogArea from '@/components/admin/Dialog/DialogArea.vue'
import useCourse from '@/composables/admin/useCourse'
// import useFetchCategories from '@/composables/admin/category/useCardCategory'
import { ElDropdownItem } from 'element-plus'
import type { Option, TListCategories, TSection } from '@/interfaces'
import SubmitButtonPrimary from '@/components/admin/Button/SubmitButtonPrimary.vue'
import ButtonSecondarySm from '@/components/admin/Button/ButtonSecondarySm.vue'
import { useRoute } from 'vue-router'
import { useCategoryStore } from '@/store/category'


const {
  fetchCourseData,
  fetchCourseLevels,
  fetchLanguages,
  submitFormEdit,
  courseLevels,
  languages,
  section,
  question,
  handlePreviewImg,
  handleFileUpload,
  handelFormSection,
  handelFormQuiz,
  handleAddLecture,
  handleEditSection,
  handleEditQuiz,
  handleDeleteSection,
  handleDeleteLecture,
  handleDeleteQuiz,
  handleSortSection,
  handleEditLecture,
  formDataEditCourse,
  formDataAddSection,
  formDataAddLecture,
  formDataAddQuiz,
  formDataEditQuiz,
  formDataEditSection,
  formDataEditLecture,
  fetchSectionId,
  fetchLectureId,
  fetchQuizId,
  dialogEditSection,
  dialogAddnewLecture,
  dialogEditLecture,
  dialogAddnewSection,
  dialogAddnewQues,
  dialogAddnewQuiz,
  dialogEditQuiz,
  dialogQues
} = useCourse()

const { categories, fetchCategoriesCRUD } = useCategoryStore()
console.log('đay là categories', categories)

console.log('Cập nhật section:', section.value);
const toggleActive = ref(false)

const ActiveToggle = (val: string[]) => {
  toggleActive.value = !toggleActive.value
}
//

const sidebarStore = useSidebarStore()
const tabPosition = ref<TabsInstance['tabPosition']>('left')
const radioValue = ref(1)
const paid = ref('1')
const linkVideo = ref<number| string>('1')
const checked2 = ref(false)

//dialog Quiz
const addChapter = () => {
  dialogAddnewSection.value = true
}
//end dialog chương

const selectedSectionId = ref<number | string>('')
const EditChapter = (id: number | string) => {
  fetchSectionId(id) // Lấy dữ liệu chương theo id
  selectedSectionId.value = id // Lưu id vào selectedSectionId
  dialogEditSection.value = true // Mở dialog\
}

//end dialog chương


//dialog bài học
const addLesson = () => {
  dialogAddnewLecture.value = true
}
const selectedLectureId = ref<number | string>('')
const editLesson = (id: number | string)  => {
  fetchLectureId(id)
  selectedLectureId.value = id
  dialogEditLecture.value = true
}
//end dialog bài học

//dialog quiz
const addQuiz = () => {
  dialogAddnewQuiz.value = true
}
const selectedQuizId = ref<number | string>('')
const EditQuiz = (id: number | string) => {
  fetchQuizId(id)
  selectedQuizId.value = id
  dialogEditQuiz.value = true
}
  //question
const handelQues = (id: number | string) => {
  fetchQuizId(id)
  dialogQues.value = true
}
const addQues = () => {
  dialogQues.value = false
  dialogAddnewQues.value = true
}
  //end question
//end dialog quiz

//dialog sắp xếp chương
const dialogSortSection = ref<boolean>(false)
const sortSection = () => {
  dialogSortSection.value = true
}
//end dialog Sort

// upload img
const imageUrl = ref<string>('') // Lưu URL hình ảnh

const updateImage = (url: string) => {
  imageUrl.value = url // Cập nhật URL hình ảnh khi nhận từ UploadGroup
}

// Hàm để xử lý khi bắt đầu kéo
const onDragStart = (event:any) => {
  console.log('Started dragging:', event);
  console.log('Item being dragged:', event.item); // Thông tin về item đang được kéo
};

// Hàm để xử lý khi kết thúc kéo
const onDragEnd = (event:any) => {
  console.log('Drag ended:', event.newIndex);
};

defineProps({
  inputId: {
    type: [String, Number],
    required: true
  }
})

onMounted(() => {
  fetchCategoriesCRUD()
  fetchCourseLevels()
  fetchLanguages()
  fetchCourseData()
})
</script>
<style>
.el-radio-group {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important; /* Chia thành 2 cột đều */
  gap: 20px !important; /* Khoảng cách giữa các item */
}

.el-radio-group .el-radio {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.el-radio-group .el-radio.is-bordered {
  padding: 10px;
}
</style>
