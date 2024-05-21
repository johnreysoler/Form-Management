<script setup>
import { setImageURL, allowMultipleSelection, RemoveObject, listStyleTypeLowerAlpha,isImgUrl,publicPath } from '../../composables/form'
import { reactive, getCurrentInstance, onMounted } from 'vue'

const props = defineProps({
    FormContent: { Type: Array },
    Form: { Type: Array }
})

const instance = getCurrentInstance();

let initialState = {
    form: []
}

let preview = reactive({ ...initialState })

function ResetPreviewForm() {
    document.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false);
    preview.form = props.FormContent
}

onMounted(()=>{
})

</script>
<template>
    <div>
        <button type="button" class="btn-icon d-block" data-bs-toggle="modal" data-bs-target="#previewModal"
            v-if="props.FormContent.length > 0" @click="ResetPreviewForm()">
            <i class="bi bi-eye-fill"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="height: 900px;overflow-y:scroll">
                    <div class="modal-body p-5">
                        <h2 class="text-center mb-5">{{ props.Form[0].title }}</h2>
                        <ul>
                            <li v-for="(content, index) in preview.form" class="mb-2 FormContainer" :key="index"
                                :class="content.isQuestion ? 'question mx-3 d-flex' : 'section d-block'">
                                <div class="row w-100">
                                    <div class="col">
                                        <div class="w-100">
                                            <div v-html="content.html"></div>
                                            <div v-if="content.images !== null"
                                                :class="[content.isquestion ? 'mt-2 mx-3' : null, 'd-flex justify-content-' + content.imagesposition]"
                                                style="flex-flow: wrap;">
                                                <img :src="isImgUrl(image.url) ? publicPath() + '/storage/' + image.url : setImageURL(image.url)"
                                                    v-for="(image, index) in content.images" :key="index"
                                                    class="mx-1 my-1" style="max-width:100%" :style="{
                height: (image.height === null ? null : (image.height * 96) + 'px'),
                width: (image.width === null ? null : (image.width * 96) + 'px')
            }">
                                            </div>
                                            <div v-if="content.isQuestion" class="my-2">
                                                <div v-if="content.optiontype.multiple_select" class="mx-3"
                                                    :style="{ display: content.optionverticalalign }">
                                                    <div class="form-check me-3"
                                                        v-for="(option, index) in content.options">
                                                        <input class="form-check-input" :type="content.optiontype.type"
                                                            :class="'question' + content.id"
                                                            :id="'input' + content.id + '_' + index"
                                                            :value="option.text" style="margin-top: 4px;" disabled
                                                            @click="allowMultipleSelection(content.multipleselect, '.question' + content.id, 'input' + content.id + '_' + index)">
                                                        <label :for="'input' + content.id + '_' + index">
                                                            <span class="me-2">{{ listStyleTypeLowerAlpha(index)
                                                                }}.</span>{{
                option.text }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check" v-if="content.otheroption">
                                                        <input class="form-check-input mt-2"
                                                            :type="content.optiontype.type"
                                                            :id="'input' + content.id + '_otheroption'"
                                                            :class="'question' + content.id" disabled
                                                            @click="allowMultipleSelection(content.multipleselect, '.question' + content.id, 'input' + content.id + '_otheroption')">
                                                        <label :for="'input' + content.id + '_otheroption'">
                                                            <span class="me-2">{{
                listStyleTypeLowerAlpha(content.options.length)
            }}.</span>Other
                                                        </label>
                                                        <input type="text" class="border ms-2 px-2 w-25 outline-none"
                                                            disabled placeholder="Type something..." />
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div v-if="content.optiontype.tag === 'text'">
                                                        <input v-if="content.optiontype.type === 'text'" type="text"
                                                            class="border p-2 w-75 outline-none" disabled
                                                            placeholder="Type something...">
                                                        <textarea v-if="content.optiontype.type === 'textarea'"
                                                            class="border p-2 w-75  outline-none" rows="5" disabled
                                                            placeholder="Type something..."
                                                            style="resize: none;"></textarea>
                                                    </div>
                                                    <div v-else>
                                                        <select class="border p-2 w-25 outline-none">
                                                            <option value="">Select...</option>
                                                            <option v-for="(option, index) in content.options"
                                                                :value="option.text">
                                                                {{ option.text }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
ul {
    counter-reset: counter;
}

ul li:not(.section) {
    counter-increment: counter;
    list-style: none;
}

ul li:not(.section):before {
    content: counter(counter) ".";
}
</style>