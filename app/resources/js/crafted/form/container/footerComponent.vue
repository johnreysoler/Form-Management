<script setup>

import { reactive } from 'vue';
const props = defineProps({
    Component: { Type: Array }
})

let initialState = {
    required: false,
}

let footer = reactive({ ...initialState })

const emit = defineEmits(['RemoveComponent','RequiredSection'])
function RemoveComponent(sectionid) {
    emit("RemoveComponent", sectionid);
}

function setQuestionRequired(){
    emit("RequiredSection", props.Component.sectionid,footer.required);
}
</script>
<template>
    <div class="footer-container">
        <div class="footer-content">
            <div class="row">
                <div class="col-8 border-end" v-if="props.Component.requiredswitch">
                    <div class="form-check form-switch p-0 d-flex mt-1">
                        <label for="flexSwitchCheckDefault"
                            class="form-check-label me-2 cursor-pointer">Required</label>
                        <input type="checkbox" role="switch" id="flexSwitchCheckDefault" v-model="footer.required" :value="footer.required"  @click="setQuestionRequired()"
                            class="form-check-input cursor-pointer m-0" style="margin-top:5px!important">
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn-icon" @click="RemoveComponent(props.Component.sectionid)"><i
                            class="bi bi-trash-fill"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>