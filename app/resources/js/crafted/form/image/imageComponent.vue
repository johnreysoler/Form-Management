<script setup>
import { onMounted, reactive } from 'vue';
import ImageModal from '../modal/Image/modal.vue';
import { setImageURL, publicPath, isImgUrl } from '../../../composables/form'
const props = defineProps({
    Component: { Type: Array },
    formCollection: { Type: Array }
})

const emit = defineEmits(['ImageProperty'])

let initialState = {
    imageCollection: [],
    imagePosition: 'start',
    ImageProperty: [{
        id: props.Component.id,
        text: null,
        html: null,
        options: null,
        required: false,
        images: null,
        otheroption: false,
        isquestion: false,
        imagesposition: 'start',
        optiontype: null,
        optionverticalalign: null

    }],
}

let image = reactive({ ...initialState })

function getImageCollection(collection) {
    image.imageCollection = collection
    sendImageProperty()
}

function sendImageProperty() {
    image.ImageProperty[0].images = image.imageCollection
    image.ImageProperty[0].imagesposition = image.imagePosition
    emit("ImageProperty", image.ImageProperty);
}

onMounted(() => {
    if (props.formCollection) {
        props.formCollection.images.forEach(item => {
            image.imageCollection.push({
                questionid: item.form_id,
                id: item.id,
                width: item.width,
                height: item.height,
                url: item.path,
                name: item.filename
            })
        });
    }
})
</script>
<template>
    <div>
        <ImageModal :Component="props.Component" @ImageCollection="getImageCollection"
            :formCollection="image.imageCollection"></ImageModal>
        <div class="row mt-2" v-if="image.imageCollection.length > 0">
            <div class="row">
                <div class="col">
                    <label>Position</label>
                    <select class="border p-1 outline-none" v-model="image.imagePosition" @change="sendImageProperty()">
                        <option value="start">Left</option>
                        <option value="center">Center</option>
                        <option value="end">Right</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex" style="flex-flow: row wrap;" :class="'justify-content-' + image.imagePosition">
                    <div :style="image.width === null ? null : { width: (image.width * 96) + 'px' }"
                        v-for="(image, index) in image.imageCollection" class="mx-1 my-1" :id="'image' + index">
                        <div :style="image.height === null ? null : { height: (image.height * 96) + 'px' }"
                            class="position-relative">
                            <img :src="isImgUrl(image.url) ? publicPath() + '/storage/' + image.url : setImageURL(image.url)" class="w-100 h-100">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>