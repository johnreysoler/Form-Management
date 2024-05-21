<script setup>
import { onMounted, reactive } from 'vue';
import QuestionComponent from '../question/questionComponent.vue';
import ImageComponent from '../image/imageComponent.vue';
import TextComponent from '../text/textComponent.vue';
import Prerequisite from '../modal/Prerequisite/modal.vue'
const props = defineProps({
    Component: { Type: Array },
    FormDetails: { Type: Array },
    FormCollection: { Type: Array },
    Index : { Type : Number}
})

let initialState = {
    SectionProperties: [],
    required: false,
    multipleselect: false,
    prerequisite: [],
    multipleselectoptiontype: false
}

let section = reactive({ ...initialState })

const emit = defineEmits(['SectionProperty', 'RemoveComponent'])

function getSectionProperty(collection) {
    section.SectionProperties = collection
    sendSectionProperties(false, null)
}

function PrerequisiteCollection(collection) {
    section.prerequisite = collection
    sendSectionProperties(false, null)
}
function sendSectionProperties(buttontrigger, switchname) {
    section.multipleselectoptiontype = section.SectionProperties[0].optiontype !== null ? section.SectionProperties[0].optiontype.multiple_select : null
    if (section.SectionProperties.length > 0) {
        switch (switchname) {
            case 'required':
                section.required = buttontrigger ? !section.required : section.required
                break;
            default:
                section.multipleselect = buttontrigger ? !section.multipleselect : section.multipleselect
                break;
        }

        section.SectionProperties[0].multipleselect = section.multipleselect
        section.SectionProperties[0].required = section.required
        section.SectionProperties[0].prerequisite = section.prerequisite

        emit("SectionProperty", section.SectionProperties);
    }
}

function RemoveComponent(id) {
    emit("RemoveComponent", id);
}

onMounted(() => {
    if (props.FormCollection[props.Index]) {
        section.required = props.FormCollection[props.Index].required
        section.multipleselect = props.FormCollection[props.Index].multipleselect
    }
})

</script>
<template>
    <div class="p-3">
        <div class="container-body">
            <QuestionComponent :Component="props.Component" @QuestionProperty="getSectionProperty"
                class="container-content" v-if="props.Component.name === 'question'"
                :formCollection="props.FormCollection[props.Index]"></QuestionComponent>
           <ImageComponent :Component="props.Component" @ImageProperty="getSectionProperty" class="container-content"
                v-else-if="props.Component.name === 'image'" :formCollection="props.FormCollection[props.Index]"></ImageComponent>
             <TextComponent :Component="props.Component" :formCollection="props.FormCollection[props.Index]"
                @TextProperty="getSectionProperty" class="container-content"
                v-else-if="props.Component.name === 'text'"></TextComponent>
        </div>
        <hr>
        <div class="container-footer ">
            <div class="footer-container">
                <div class="footer-content">
                    <div class="row px-3">
                        <div class="col">
                            <Prerequisite :Component="props.Component" :FormDetails="props.FormDetails"
                                :FormCollection="props.FormCollection" @PrerequisiteCollection="PrerequisiteCollection"
                                :key="props.Component.id"></Prerequisite>
                        </div>
                        <div class="border-end d-flex"
                            :class="section.multipleselectoptiontype ? 'col-3' : 'col justify-content-end'"
                            v-if="props.Component.requiredswitch">
                            <div class="form-check form-switch p-0 d-flex mt-1 me-3">
                                <label for="flexSwitchCheckDefault"
                                    class="form-check-label me-2 cursor-pointer">Required</label>
                                <input type="checkbox" role="switch" id="flexSwitchCheckDefault"
                                    v-model="section.required" :value="section.required"
                                    @click="sendSectionProperties(true, 'required')"
                                    class="form-check-input cursor-pointer m-0" style="margin-top:5px!important">
                            </div>
                            <div class="form-check form-switch p-0 d-flex mt-1" v-if="section.multipleselectoptiontype">
                                <label for="flexSwitchCheckDefault"
                                    class="form-check-label me-2 cursor-pointer">Multiple Select</label>
                                <input type="checkbox" role="switch" id="flexSwitchCheckDefault"
                                    v-model="section.multipleselect" :value="section.multipleselect"
                                    @click="sendSectionProperties(true, 'multipleselect')"
                                    class="form-check-input cursor-pointer m-0" style="margin-top:5px!important">
                            </div>
                        </div>
                        <div class="col-1">
                            <button class="btn-icon" @click="RemoveComponent(props.Component.id)"><i
                                    class="bi bi-trash-fill"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>