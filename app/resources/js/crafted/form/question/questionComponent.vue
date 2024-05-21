<script setup>
import { onMounted, reactive } from 'vue';
import axios from 'axios'
import optionComponent from './option/optionComponent.vue';
import { RemoveObject, setImageURL, ValidateOptionCollection, ValidateQuestionCollection, publicPath, isImgUrl } from '../../../composables/form'
import ImageModal from '../modal/Image/modal.vue';

const emit = defineEmits(['QuestionProperty'])


const props = defineProps({
    Component: { Type: Array },
    formCollection: { Type: Array }
})

let initialState = {
    id: 0,
    QuestionProperty: [],
    OptionTypeCollection: [],
    OptionType: 1,
    OptionTypeProperty: [],
    addOtherOption: false,
    addOption: 1,
    content: null,
    optionCollection: [],
    optionVerticalAlign: 'block',
    imageCollection: [],
    imagePosition: 'start'
}

let question = reactive({ ...initialState })



function getImageCollection(collection) {
    question.imageCollection = collection
    sendQuestionProperty()
}

function getOptionCollection(questionid, optionid, text) {
    let collection = ValidateOptionCollection(question.optionCollection, questionid, optionid)
    if (collection[0] === undefined || collection.length === 0) {
        question.optionCollection.push({
            questionid: questionid,
            optionid: optionid,
            text: text
        })
    }
    else {
        collection[0].text = text
    }
    sendQuestionProperty()
}

function getOptionType() {
    axios.get('/optiontype').then(function (response) {
        question.OptionTypeCollection = response.data
        getOptionTypeProperty()
    })
}

function getOptionTypeProperty() {
    question.OptionTypeProperty = question.OptionTypeCollection.filter(o => o.id === question.OptionType)[0]
    question.addOption = question.OptionTypeProperty.multiple_select ? question.addOption : 1
    sendQuestionProperty()
}

function sendQuestionProperty() {
    let validatecollection = ValidateQuestionCollection(question.QuestionProperty, question.id)[0]
    if (validatecollection === undefined || validatecollection.length === 0) {
        question.QuestionProperty.push({
            id: question.id,
            text: question.content.textContent,
            html: question.content.innerHTML,
            options: question.optionCollection,
            required: false,
            multipleselect: false,
            images: question.imageCollection,
            imagesposition: question.imagePosition,
            otheroption: question.addOtherOption,
            isquestion: true,
            optiontype: question.OptionTypeProperty,
            optionverticalalign: question.optionVerticalAlign
        })
    }
    else {
        validatecollection.text = question.content.textContent,
            validatecollection.html = question.content.innerHTML,
            validatecollection.options = question.optionCollection,
            validatecollection.images = question.imageCollection,
            validatecollection.otheroption = question.addOtherOption,
            validatecollection.optiontype = question.OptionTypeProperty,
            validatecollection.imagesposition = question.imagePosition,
            validatecollection.optionverticalalign = question.optionVerticalAlign
    }
    emit("QuestionProperty", question.QuestionProperty);
}

function RemoveOptionObject(id, index) {
    RemoveObject(id)
    question.optionCollection = question.optionCollection.filter(o => o.questionid === question.id && o.optionid !== index)
    sendQuestionProperty()
}

function AddOtherOption(boolean) {
    question.addOtherOption = boolean
    sendQuestionProperty()
}



onMounted(() => {
    getOptionType()
    question.content = document.getElementById('question' + props.Component.id)
    question.id = props.Component.id
    if (props.formCollection) {
        question.content.innerHTML = props.formCollection.html
        question.OptionType = props.formCollection.optiontype.id
        question.imagePosition = props.formCollection.imagesposition
        question.optionVerticalAlign = props.formCollection.optionverticalalign
        question.addOtherOption = props.formCollection.otheroption
        question.addOption = props.formCollection.options.length
        props.formCollection.images.forEach(item => {
            question.imageCollection.push({
                questionid: item.form_id,
                id: item.id,
                width: item.width,
                height: item.height,
                url: item.path,
                name: item.filename
            })
        });
        props.formCollection.options.forEach(item => {
            question.optionCollection.push({
                questionid: item.section_id,
                optionid: item.id,
                text: item.text
            })
        });
    }

})
</script>
<template>
    <div class="question-container">
        <div class="question-content">
            <div class="row">
                <div class="row">
                    <div class="col">
                        <div contenteditable="true" class="border-bottom w-100 outline-none question px-2 py-1"
                            :id="'question' + props.Component.id" placeholder="Untitled Question"
                            @input="sendQuestionProperty()"></div>
                    </div>
                    <div class="col-2 d-flex" style="height: fit-content">
                        <ImageModal :Component="props.Component" @ImageCollection="getImageCollection"
                            :formCollection="question.imageCollection"></ImageModal>
                        <select class="border p-1 w-100" v-model="question.OptionType"
                            @change="getOptionTypeProperty()">
                            <option v-for="(optiontype, index) in question.OptionTypeCollection" :key="index"
                                :value="optiontype.id">{{ optiontype.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2" v-if="question.imageCollection.length > 0">
                    <div class="row">
                        <div class="col">
                            <label>Position</label>
                            <select class="border p-1 outline-none" v-model="question.imagePosition"
                                @change="sendQuestionProperty()">
                                <option value="start">Left</option>
                                <option value="center">Center</option>
                                <option value="end">Right</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex" style="flex-flow: row wrap;"
                            :class="'justify-content-' + question.imagePosition">
                            <div :style="image.width === null ? null : { width: (image.width * 96) + 'px' }"
                                v-for="(image, index) in question.imageCollection" class="mx-1 my-1"
                                :id="'image' + index">
                                <div :style="image.height === null ? null : { height: (image.height * 96) + 'px' }"
                                    class="position-relative">
                                    <img :src="isImgUrl(image.url) ? publicPath() + '/storage/' + image.url : setImageURL(image.url)"
                                        class="w-100 h-100">

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-2">
                    <div class="col" :class="question.OptionTypeProperty.multiple_select ? 'mx-4' : null">
                        <div class="row" v-if="question.OptionTypeProperty.multiple_select">
                            <div class="col">
                                <label class="me-2">Alignment</label>
                                <select class="border p-1 outline-none" v-model="question.optionVerticalAlign"
                                    @change="sendQuestionProperty()">
                                    <option value="block">Vertical</option>
                                    <option value="flex">Horizontal</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2" v-for="(option, index) in question.addOption"
                            :id="'option_' + index + '_' + props.Component.id">
                            <div class="col">
                                <optionComponent :OptionId="index" :QuestionId="props.Component.id"
                                    :OptionProperty="question.OptionTypeProperty" :Component="props.Component"
                                    @OptionCollection="getOptionCollection" :formCollection="props.formCollection"
                                    :OptionColletion="question.optionCollection">
                                </optionComponent>
                            </div>
                            <div class="col" v-if="question.OptionTypeProperty.multiple_select">
                                <button class="btn-icon d-block me-2 btn-remove-option" title="Remove option"
                                    @click="RemoveOptionObject('option_' + index + '_' + props.Component.id, index)">
                                    <i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="row mb-2" v-if="question.addOtherOption" :id="'option_0' + props.Component.id">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" :type="question.OptionTypeProperty.type"
                                        name="flexRadioDefault" id="flexRadioDefault1" style="margin-top:9px" disabled>
                                    <label class="form-check-label ms-2" for="flexRadioDefault1">
                                        Other
                                    </label>
                                    <input type="text" class="border ms-2 p-1 px-2 w-75" disabled
                                        placeholder="Type something..." />
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn-icon d-block me-2 btn-remove-option" title="Remove option"
                                    @click="AddOtherOption(false)">
                                    <i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="row" v-if="question.OptionTypeProperty.multiple_select">
                            <div class="col d-flex">
                                <button class="btn btn-light btn-sm" @click="question.addOption++">Add option</button>
                                <div v-if="!question.addOtherOption">
                                    <span>or</span>
                                    <button class="btn btn-link btn-sm" @click="AddOtherOption(true)">Add
                                        "Other"</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>