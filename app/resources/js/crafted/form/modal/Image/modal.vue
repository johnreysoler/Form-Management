<script setup>
import { onMounted, reactive } from 'vue';
import { setImageURL, RemoveFromCollection, ValidateImageFileType, ValidateImageSize, publicPath,isImgUrl } from '../../../../composables/form'

let props = defineProps({
    Component: { Type: Array },
    GetImageCollection: { Type: Array },
    formCollection: { Type: Array },
})

let emit = defineEmits(['ImageCollection'])
let initialState = {
    input: null,
    close: null,
    imageWidth: null,
    imageHeight: null,
    imageCollection: [],
    id: 1
}

let modal = reactive({ ...initialState })

function GetImageFile() {
    const file = modal.input.files
    if (file.length > 0) {
        if (file !== undefined) {
            if (ValidateImageFileType(file)) {
                AddImageToCollection(null, file)
            }
            else {
                alert('invalid file type')
            }
        }
    }
}

function AddImageToCollection(id, file) {
    if (id === null) {
        modal.imageCollection.push({
            questionid: props.Component.id,
            id: modal.id++,
            width: null,
            height: null,
            url: file[0],
            name: file[0].name
        })
    }
    else {
        modal.imageCollection[id].height = ValidateImageSize(modal.imageHeight)
        modal.imageCollection[id].width = ValidateImageSize(modal.imageWidth)
    }
}


function SaveImageFile() {
    emit("ImageCollection", modal.imageCollection);
    modal.close.click()
}

function setImageSize(id) {
    modal.imageWidth = document.getElementById('width' + props.Component.id + '_' + id).value
    modal.imageHeight = document.getElementById('height' + props.Component.id + '_' + id).value
    AddImageToCollection(id, null)
}

function RemoveImage(id) {
    modal.imageCollection = RemoveFromCollection(modal.imageCollection, id)
}

function GetImageCollection() {
    modal.input = document.getElementById('InputImage' + props.Component.id)
    modal.close = document.getElementById('btn_close_modal' + props.Component.id)
    modal.imageCollection = []
    if (props.formCollection) {
        props.formCollection.forEach(item => {
            modal.imageCollection.push({
                questionid: props.Component.id,
                id: item.id,
                width: item.width,
                height: item.height,
                url: item.url,
                name: item.filename
            })
        });
    }
}

function Browse() {
    modal.input.value = null
    modal.input.click()
}
onMounted(() => {
})
</script>
<template>
    <div>
        <button class="me-2 outline-none"
            :class="props.Component.name === 'question' ? 'btn-icon d-block' : 'btn btn-link'" title="Add image"
            data-bs-toggle="modal" :data-bs-target="`#ImageModal${props.Component.id}`" @click="GetImageCollection()">
            <i class="bi bi-image-fill" v-if="props.Component.name === 'question'"></i>
            <span v-else>Add Image</span>
        </button>
        <div class="modal fade" :id="'ImageModal' + props.Component.id" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="ImageModalLabel">Insert Image</h1>
                    </div>
                    <div class="modal-body d-flex" style="flex-flow: row wrap;max-height: 500px;overflow-y: auto;">
                        <div style="width: 15rem;" class="mx-1 my-1">
                            <div style="height:16rem;" class="card">
                                <input type="file" hidden :id="'InputImage' + props.Component.id"
                                    @change="GetImageFile()" accept="image/*">
                                <button type="button" class="btn btn-light w-100 h-100" @click="Browse()">+
                                    Add
                                    Image</button>
                            </div>
                        </div>
                        <div style="width: 15rem;" v-for="(image, index) in modal.imageCollection"
                            class="mx-1 my-1 image" :id="'image' + index">
                            <div style="height:16rem;" class="position-relative">
                                <button class="position-absolute btn-icon btn-remove-image"><i class="bi bi-x fw-bold"
                                        @click="RemoveImage(image.id)" style="font-size:25px"></i></button>
                                <img :src="isImgUrl(image.url) ? publicPath() + '/storage/' + image.url : setImageURL(image.url)"
                                 style="height:100%;width:100%" class="rounded">
                            </div>
                            <div class="row mt-2 d-flex justify-content-center">
                                <div class="col-8 d-flex">
                                    <input type="text" class="border p-1 text-center w-100 outline-none" :value="image.width" placeholder="W"
                                        @change="setImageSize(index)" :id="'width' + props.Component.id + '_' + index">
                                    <label class="mx-2 mt-1 fw-bold">x</label>
                                    <input type="text" class="border p-1 text-center w-100 outline-none" :value="image.height" placeholder="H"
                                        :id="'height' + props.Component.id + '_' + index" @change="setImageSize(index)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            :id="'btn_close_modal' + props.Component.id">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="SaveImageFile()">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>