<script setup>
import { reactive, onMounted } from 'vue'
const props = defineProps({
    Component: { Type: Array },
    FormDetails: { Type: Array },
    FormCollection: { Type: Array }
})

const emit = defineEmits(['PrerequisiteCollection'])

let initialState = {
    collection: [],
    Addprerequisite: 1,
    form_id: null,
    question_id: null,
    option_id: null,
    close: null,
}
let options = reactive({
    forms: [],
    questions: [],
    options: []
})
let prerequisite = reactive({ ...initialState })


function fetchForm() {
    if(props.FormCollection){
        let prerequisites = props.FormCollection
        // console.log(prerequisites)
        // if(prerequisites !== undefined){
        //     prerequisite.form_id = prerequisites.prerequisite_form_id
        //     prerequisite.question_id = prerequisites.prerequisite_section_id
        //     fetchPrerequisiteQuestion()
        // }
        
    }

    axios.get('/fetchOptions').then(function (response) {
        options.forms = response.data.Forms.filter(o=> o.id !== props.FormDetails[0].id)
    })
}

function fetchPrerequisiteQuestion() {
    options.questions = []
    if (prerequisite.form_id === props.FormDetails[0].id) {
        props.FormCollection.forEach(item => {
            if (item.id !== props.Component.id) {
                if(item.isQuestion && item.multipleselect){
                    options.questions.push({
                    id: item.id,
                    text: item.text
                })
                }
                
            }
        });
    }
    else {
        axios.get('/fetchPrerequisiteQuestion/' + prerequisite.form_id).then(function (response) {
            options.questions = response.data
        })
    }
    options.options = []
}

function fetchPrerequisiteOption() {
    if (prerequisite.form_id === props.FormDetails[0].id) {
        let question_options = props.FormCollection.filter(o => o.id === prerequisite.question_id)[0].options
        question_options.forEach(item => {
            options.options.push({
                id: item.optionid,
                text: item.text
            })
        });
    }
    else {
        axios.get('/fetchPrerequisiteOption/' + prerequisite.question_id).then(function (response) {
            options.options = response.data
        })
    }
}

function AddPrerequisiteToCollection() {
    if (prerequisite.collection.length === 0) {
        prerequisite.collection.push({
            questionid: props.FormDetails[0].id,
            prerequisiteform: prerequisite.form_id,
            prerequisitequestion: prerequisite.question_id,
            prerequisiteanswer: prerequisite.option_id
        })
    }
    else{
        prerequisite.collection[0].prerequisiteform = prerequisite.form_id,
        prerequisite.collection[0].prerequisitequestion = prerequisite.question_id,
        prerequisite.collection[0].prerequisiteanswer = prerequisite.option_id
    }
}

function SavePrerequisite() {
    AddPrerequisiteToCollection() 
    emit("PrerequisiteCollection", prerequisite.collection);
    prerequisite.close.click()
}
onMounted(() => {
    fetchForm()
    prerequisite.close = document.getElementById('btn_close_prerequisite_modal' + props.Component.id)
})
</script>
<template>
    <div>
        <button type="button" class="btn btn-link btn-sm" data-bs-toggle="modal"
            :data-bs-target="`#prerequisiteModal${props.Component.id}`" @click="fetchForm()">Add pre-requisite</button>
        <div class="modal fade" :id="'prerequisiteModal' + props.Component.id" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="prerequisiteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="prerequisiteModalLabel">Add pre-requisite</h1>
                    </div>
                    <div class="modal-body" style="max-height:350px;overflow-y:scroll">
                        <div class="moda-body-content">
                            <div class="row mb-1">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        <label class="mt-1">Form</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-select" v-model="prerequisite.form_id"
                                            :key="props.FormDetails[0].id" @change="fetchPrerequisiteQuestion()">
                                            <option :value="props.FormDetails[0].id">
                                                Current Form</option>
                                            <option v-for="(form, index) in options.forms" :value="form.id"
                                                :key="index">
                                                {{ form.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        <label class="mt-1">Question</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-select" v-model="prerequisite.question_id"
                                            :key="props.FormDetails[0].id" @change="fetchPrerequisiteOption()">
                                            <option v-for="(question, index) in options.questions" :value="question.id"
                                                :key="index">
                                                {{ question.text }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3">
                                        <label class="mt-1">Answer</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-select" v-model="prerequisite.option_id"
                                            :key="props.FormDetails[0].id">
                                            <option v-for="(option, index) in options.options" :value="option.text"
                                                :key="index">
                                                {{ option.text }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" :id="'btn_close_prerequisite_modal' + props.Component.id">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="SavePrerequisite()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>