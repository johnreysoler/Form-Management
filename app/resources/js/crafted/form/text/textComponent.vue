<script setup>
import {reactive,onMounted} from 'vue'
const props = defineProps({
    Component: { Type: Array },
    formCollection: { Type: String }
})


const emit = defineEmits(['TextProperty'])
let initialState = {
    content : null,
    TextProperty: [{
        id : props.Component.id,
        text : null,
        html : null,
        options : null,
        required : false,
        images : null,
        otheroption : false,
        isquestion : false,
        optiontype: null,
        optionverticalalign : null
    }],
}

let text = reactive({ ...initialState })


function sendTextProperty(){
    text.TextProperty[0].text = text.content.textContent
    text.TextProperty[0].html = text.content.innerHTML
    emit("TextProperty", text.TextProperty);
}

onMounted(()=>{
    text.content = document.getElementById('text'+props.Component.id)
    if(props.formCollection){
        text.content.innerHTML = props.formCollection.html
    }
})
</script>
<template>  
    <div>
        <div contenteditable="true" class="border w-100 outline-none question px-2 py-1"
            placeholder="Type something..." style="height: 300px;overflow-y:scroll" :id="'text'+props.Component.id" @input="sendTextProperty()"></div>
    </div>
</template>