<script setup>
import axios from 'axios'
import { onMounted, reactive } from 'vue'
import Createmodal from '../../crafted/form/modal/Index/Createmodal.vue'
import Editmodal from '../../crafted/form/modal/Index/Editmodal.vue'
let initialState = {
    forms: [],
    search: null,
    modal: {
        show : false
    }
}

let formData = reactive({ ...initialState })

function fetchData(url = '/form/search') {
    axios.get(url, {
        params: {
            search: formData.search,
        }
    }).then(function (response) {
        formData.forms = response.data.forms
    })
}

function PublishForm(formid){
    axios.patch('/form/publish/'+formid).then(function (response) {
        fetchData()
    })
}
onMounted(() => {
    fetchData()
})
</script>
<template>
    <div class="container">
        <div class="container-fluid">
            <div class="card my-5">
                <div class="card-body" style="height: 90vh;">
                    <div>
                        <div class="row">
                            <div class="col-3">
                                <input type="text" class="form-control" placeholder="Search" @input="fetchData()"
                                    v-model="formData.search">
                            </div>
                            <div class="col d-flex justify-content-end">
                                <Createmodal :FormDetails="null" :FormId="null"></Createmodal>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col">
                                <div class="table-container p-2">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Assign by</th>
                                                <th>Start date</th>
                                                <th>End date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(form, index) in formData.forms" :key="index">
                                                <td>{{ form.id }}</td>
                                                <td>{{ form.title }}</td>
                                                <td>{{ form.description }}</td>
                                                <td>{{ form.statuses.name }}</td>
                                                <td>{{ form.assignments.name}}</td>
                                                <td>{{ form.start_date }}</td>
                                                <td>{{ form.end_date }}</td>
                                                <td>
                                                    <div class="dropdown" :class="form.statuses.id !== 3 && form.statuses.id !== 1  ? 'd-none' : 'd-block'">
                                                        <button class="btn btn-light dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false" @click="">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton1">
                                                            <a class="dropdown-item" href="#" @click="PublishForm(form.id)" 
                                                            :class="form.statuses.id === 1 ? 'd-none' : null">Publish</a>
                                                            <Createmodal :FormDetails="form" :FormId="form.id"></Createmodal>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>