<script setup>
import axios from 'axios'
import { onMounted, reactive } from 'vue'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'

import { allowMultipleSelection, listStyleTypeLowerAlpha, publicPath } from '../../composables/form'
const props = defineProps({
    userform: { Type: Array },
    user_id: { Type: Number }
})

let initialState = {
    userForm: [],
    title: '',
    id: 0,
    response: []
}

let form = reactive({ ...initialState })

function fetchForm() {
    axios.get('/form/user/' + props.userform.id).then(function (response) {
        form.userForm = response.data[0].sections
        form.title = response.data[0].title
    })
}

function SubmitResponse() {
    let requiredQuestion = form.userForm.filter(o => o.is_required === 1)
    let filledupfields = requiredQuestion.length
    requiredQuestion.forEach((item) => {
        let requiredfield = document.getElementById('required' + item.id)
        let checkuserresponse = form.response.filter(o => o.questionid === item.id).length
        if (checkuserresponse > 0) {
            requiredfield.classList.add('d-none')
            filledupfields -= 1
        }
        else {
            requiredfield.classList.remove("d-none")
        }
    });

    if (filledupfields === 0) {
        axios.post('/form/user/response', {
            user_id: props.user_id,
            form_id: props.userform.id,
            user_response: form.response
        }).then(function (response) {
            Swal.fire("Saved!", "", "success");
            window.location.reload();
        })
    }

}

function getResponse(is_multiple_select, className, Elementid, questionid, optionid, optiontext, prerequisite) {
    if (prerequisite.length > 0) {
        if (prerequisite[0].section_id !== null) {
            let validateprerequisite = prerequisite.filter(o => o.answer === optiontext)
            if (prerequisite.length > 0) {
                if (validateprerequisite.length > 0) {
                    document.getElementById('section' + prerequisite[0].section_id).classList.remove("d-none");
                    document.getElementById('section' + prerequisite[0].section_id).classList.add("d-flex");
                }
                else {
                    document.getElementById('section' + prerequisite[0].section_id).classList.add("d-none");
                }

            }
        }
    }

    allowMultipleSelection(is_multiple_select, className, Elementid)

    if (optiontext === null) {

        if (optionid !== 0) {
            optiontext = document.getElementById('otheroption' + questionid).value
        }
        else {
            optiontext = document.getElementById('input' + questionid).value
        }
    }

    let validateResponse = form.response.filter(o => o.questionid === questionid)
    if (validateResponse.length === 0) {
        form.response.push({
            questionid: questionid,
            optionid: optionid,
            text: optiontext
        })
    }
    else {
        if (is_multiple_select) {
            validateResponse = validateResponse.filter(o => o.optionid === optionid)
            if (validateResponse.length === 0) {
                form.response.push({
                    questionid: questionid,
                    optionid: optionid,
                    text: optiontext
                })
            }
            else {
                validateResponse[0].optionid = optionid
                validateResponse[0].text = optiontext
            }
        }
        else {
            validateResponse[0].optionid = optionid
            validateResponse[0].text = optiontext
        }
    }
}

function Validatesection(section) {
    let className;
    if (section.is_question) {
        if (section.prerequisite === 0) {
            className = 'question mx-3 d-flex'
        }
        else {
            if (section.prerequisites.length === 0) {
                if (section.prerequisite === 1) {
                    className = 'question mx-3 d-none'
                }
                else {
                    className = 'question mx-3 d-flex'
                }
            }
            else {
                className = 'question mx-3 d-none'
            }
        }

    }
    else {
        className = 'section d-block'
    }
    return className
}
onMounted(() => {
    fetchForm()
})
</script>
<template>
    <div class="container">
        <div class="userForm">
            <div class="content p-5">
                <h2 class="text-center mb-3">{{ form.title }}</h2>
                <ul style="height:83vh;overflow-y:scroll">
                    <li v-for="(content, index) in form.userForm" class="mb-2 FormContainer" :key="index"
                        :id="'section' + content.id" :class="Validatesection(content)">
                        <div class="px-2 w-100">
                            <div v-html="content.html"></div>
                            <span class="text-danger d-none" :id="'required' + content.id"
                                v-if="content.is_question && content.is_required">*
                                Required</span>
                            <div v-if="content.images !== null"
                                :class="[content.isquestion ? 'mt-2 mx-3' : null, 'd-flex justify-content-' + content.image_position]"
                                style="flex-flow: wrap;">
                                <img :src="publicPath() + '/storage/' + image.path"
                                    v-for="(image, index) in content.images" :key="index" class="mx-1 my-1"
                                    style="max-width:100%" :style="{
                    height: (image.height === null ? null : (image.height * 96) + 'px'),
                    width: (image.width === null ? null : (image.width * 96) + 'px')
                }">
                            </div>
                            <div v-if="content.is_question" class="my-2">
                                <div v-if="content.option_types.multiple_select" class="mx-3"
                                    :style="{ display: content.option_vertical_align }">
                                    <div class="form-check me-3" v-for="(option, index) in content.options">
                                        <input class="form-check-input" :type="content.option_types.type"
                                            :class="'question' + content.id" :id="'input' + content.id + '_' + index"
                                            :value="option.text" style="margin-top: 4px;" @click="getResponse(content.is_multiple_select === 0 ? false : true,
                    '.question' + content.id, 'input' + content.id + '_' + index,
                    content.id, option.id, option.text, content.prerequisites)">
                                        <label :for="'input' + content.id + '_' + index">
                                            <span class="me-2">{{ listStyleTypeLowerAlpha(index) }}.</span>{{
                    option.text
                }}
                                        </label>
                                    </div>
                                    <div class="form-check" v-if="content.with_other_option">
                                        <input class="form-check-input mt-2" :type="content.option_types.type"
                                            :id="'input' + content.id + '_otheroption'" @input="getResponse(content.is_multiple_select === 0 ? false : true,
                    '.question' + content.id, 'input' + content.id + '_otheroption',
                    content.id, content.options.length, null)" :class="'question' + content.id">
                                        <label :for="'input' + content.id + '_otheroption'">
                                            <span class="me-2">{{
                    listStyleTypeLowerAlpha(content.options.length)
                }}.</span>Other
                                        </label>
                                        <input type="text" class="border ms-2 px-2 w-25 outline-none"
                                            :id="'otheroption' + content.id" @input="getResponse(content.is_multiple_select === 0 ? false : true,
                    '.question' + content.id, 'input' + content.id + '_' + index,
                    content.id, content.options.length, null, content.prerequisites)"
                                            placeholder="Type something..." />
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-if="content.option_types.tag === 'text'">
                                        <input v-if="content.option_types.type === 'text'" type="text"
                                            :id="'input' + content.id" class="border p-2 w-75 outline-none"
                                            placeholder="Type something..." @input="getResponse(null, null, null,
                    content.id, content.options.length, null, content.prerequisites)">
                                        <textarea v-if="content.option_types.type === 'textarea'"
                                            class="border p-2 w-75  outline-none" rows="5" :id="'input' + content.id"
                                            placeholder="Type something..." style="resize: none;" @input="getResponse(null, null, null,
                    content.id, content.options.length, null, content.prerequisites)"></textarea>
                                    </div>
                                    <div v-else>
                                        <select class="border p-2 w-25 outline-none" :id="'input' + content.id" @input="getResponse(null, null, null,
                    content.id, 0, null,content.prerequisites)">
                                            <option value="">Select...</option>
                                            <option v-for="(option, index) in content.options" :value="option.text">
                                                {{
                                                option.text }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div>
                    <button type="button" class="btn btn-success btn-sm" @click="SubmitResponse()">Submit</button>
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