<script setup>
import axios from 'axios'
import { onMounted, reactive } from 'vue'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'
import Modal from '../Dropdown/modal.vue';

const props = defineProps({
    FormDetails: { Type: Array }
})
let initialState = {
    title: null,
    description: null,
    assignment: 1,
    assignto: [],
    start_date: null,
    end_date: null,
    form_id: null,
    question_id: null,
    option_id: null
}

let options = reactive({
    assignments: [],
    assigntos: [],
    forms: [],
    questions: [],
    options: []
})


let form = reactive({ ...initialState })

function SaveForm() {
    axios.post('/form/store', {
        title: form.title,
        description: form.description,
        startdate: form.start_date,
        enddate: form.end_date,
        assignment: form.assignment,
        assignto: form.assignto,
        form: form.form_id,
        question: form.question_id,
        option: form.option_id
    }).then(function (response) {
        Swal.fire("Saved!", "", "success");
        window.location.href = '/form/create/' + response.data
    })
}

function fetchData() {
    axios.get('/fetchOptions').then(function (response) {
        options.assignments = response.data.Assignments
        options.forms = response.data.Forms
    })
}

function fetchPrerequisiteQuestion() {
    axios.get('/fetchPrerequisiteQuestion/' + form.form_id).then(function (response) {
        options.questions = response.data
    })
}

function fetchPrerequisiteOption() {
    axios.get('/fetchPrerequisiteOption/' + form.question_id).then(function (response) {
        options.options = response.data
    })
}

function GetAssignmentRespondent() {
    axios.get('/assignment/' + form.assignment).then(function (response) {
        options.assigntos = response.data
    })
}
onMounted(() => {
    fetchData()
    GetAssignmentRespondent()
})

</script>
<template>
    <div>
        <!-- <a class="dropdown-item" href="#" data-bs-toggle="modal"
            data-bs-target="#formEditModal">Edit</a> -->
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
            :data-bs-target="`#formEditModal${props.FormDetails.id}`">{{ props.FormDetails.id }}</button>

        <div class="modal fade" :id="'formEditModal' + props.FormDetails.id" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="formEditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="formEditModalLabel">Create new form</h1>
                    </div>
                    <div class="modal-body">
                        dlmas,dl;asm;dlas
                        <!-- <div class="moda-body-content">
                            <div class="row">
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1">Title</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" v-model="form.title">
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1">Description</label>
                                        </div>
                                        <div class="col">
                                            <textarea class="form-control" id="" cols="30" rows="5"
                                                style="resize: none;" v-model="form.description"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1">Assign by</label>
                                        </div>
                                        <div class="col">
                                            <select class="form-select" v-model="form.assignment"
                                                @change="GetAssignmentRespondent()">
                                                <option v-for="(assignment, index) in options.assignments"
                                                    :value="assignment.id" :key="index">
                                                    {{ assignment.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1"></label>
                                        </div>
                                        <div class="col">
                                            <select class="form-select" v-model="form.assignto">
                                                <option v-for="(assign, index) in options.assigntos" :value="assign.id"
                                                    :key="index">
                                                    {{ assign.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1">Start Date</label>
                                        </div>
                                        <div class="col">
                                            <input type="date" class="form-control" v-model="form.start_date" />
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-3">
                                            <label class="mt-1">End Date</label>
                                        </div>
                                        <div class="col">
                                            <input type="date" class="form-control" v-model="form.end_date" />
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <fieldset>
                                            <span>Pre-requisite</span>
                                            <div class="row mb-1">
                                                <div class="col-3">
                                                    <label class="mt-1">Form</label>
                                                </div>
                                                <div class="col">
                                                    <select class="form-select" v-model="form.form_id"
                                                        @change="fetchPrerequisiteQuestion()">
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
                                                    <select class="form-select" v-model="form.question_id"
                                                        @change="fetchPrerequisiteOption()">
                                                        <option v-for="(question, index) in options.questions"
                                                            :value="question.id" :key="index">
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
                                                    <select class="form-select" v-model="form.option_id">
                                                        <option v-for="(option, index) in options.options"
                                                            :value="option.text" :key="index">
                                                            {{ option.text }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="SaveForm()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>