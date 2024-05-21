<script setup>
import { onMounted, reactive } from 'vue';
import { RemoveObject, RemoveFromCollection } from '../../../../composables/form'
let props = defineProps({
    Component: { Type: Array },
    formCollection: { Type: Array }
})

let emit = defineEmits(['setDropdownOptionText'])
let initialState = {
    text: [],
    addOption: 1,
    collection: [],
    close: null,
    id: 0
}

let option = reactive({ ...initialState })

function RemoveOptionObject(dropdownoptionitem, index) {
    RemoveObject(dropdownoptionitem)
    option.collection = RemoveFromCollection(option.collection, index)
}

function setOptionText(index) {
    if(option.collection[index] === undefined){
        option.collection.push({
            id: option.collection[option.collection.length - 1].id + 1,
            questionid: props.Component.id,
            text: option.text[index]
        })
    }
    else{
        option.collection[index].text = option.text[index]
    }
}

function sendOptionProperty() {
    emit("setDropdownOptionText", option.collection);
    option.close.click()
}

function GetDropdownCollection() {
    if (props.formCollection) {
        option.collection = []
        option.addOption = props.formCollection.length
        option.id = (props.formCollection[props.formCollection.length - 1].optionid)

        props.formCollection.forEach((item, index) => {
            option.collection.push({
                id: item.optionid,
                questionid: item.questionid,
                text: item.text
            })
            option.text[index] = item.text
        });

    }
}
onMounted(() => {
    option.close = document.getElementById('btn_close_modal_option' + props.Component.id)

})
</script>
<template>
    <div class="mt-1 mx-2">
        <button class="btn btn-link btn-sm outline-none" title="Add dropdown option" data-bs-toggle="modal"
            :data-bs-target="`#DropdpwnModal${props.Component.id}`" @click="GetDropdownCollection()">
            Add Option
        </button>
        <div class="modal fade" :id="'DropdpwnModal' + props.Component.id" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="ImageModalLabel">Add option</h1>
                    </div>
                    <div class="modal-body" style="max-height: 250px; overflow-y: scroll">
                        <div class="row w-100 my-1" v-for="(dropdown, index) in option.addOption"
                            :id="'option_' + index + '_' + props.Component.id">
                            <div class="col">
                                <input type="text"
                                    class="border-bottom border-0 outline-none option ms-2 p-1 px-2 w-100 bg-transparent"
                                    v-model="option.text[index]" :id="'ddoption' + props.Component.id + '_' + index"
                                    placeholder="Option" @input="setOptionText(index)" />
                            </div>
                            <div class="col-1">
                                <button class="btn-icon d-block btn-remove-option" title="Remove option"
                                    @click="RemoveOptionObject('option_' + index + '_' + props.Component.id, index)">
                                    <i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="row my-1">
                            <div class="col">
                                <button class="btn btn-link btn-sm" @click="option.addOption++">Add option</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            :id="'btn_close_modal_option' + props.Component.id">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="sendOptionProperty()">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>