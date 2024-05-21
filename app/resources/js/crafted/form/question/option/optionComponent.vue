<script setup>
import { onMounted, reactive } from 'vue';
import DropdownModal from '../../modal/Dropdown/modal.vue';
let initialState = {
    text: null,
    dropdownOption: null,
    dropdownSelection: []
}

let option = reactive({ ...initialState })

let emit = defineEmits(['OptionCollection'])

const props = defineProps({
    OptionProperty: { Type: Array },
    OptionId: { Type: Number },
    QuestionId: { Type: Number },
    Component: { Type: Array },
    formCollection: { Type: Array },
    OptionColletion: { Type: Array },
})

function setOptionText() {
    let optionid = option.dropdownOption === null ? props.OptionId : option.dropdownOption
    emit("OptionCollection", props.QuestionId, optionid, option.text);
}

function getDropdownText(DropdownProperty) {
    option.dropdownSelection = DropdownProperty
    DropdownProperty.forEach(item => {
        option.text = item.text
        option.dropdownOption = item.id
        setOptionText()
    });
}
onMounted(() => {
    option.text = null
    if (props.formCollection) {
        if (props.formCollection.optiontype.tag === 'select') {
            option.dropdownSelection = props.formCollection.options
        }
        else {
            option.text = props.formCollection.options[props.OptionId] === undefined ? null :
            props.formCollection.options[props.OptionId].text
        }
    }

})
</script>
<template>
    <div class="row">
        <div class="col">
            <div class="form-check" v-if="props.OptionProperty.tag === 'input'">
                <input :type="props.OptionProperty.type" name="flexRadioDefault" id="flexRadioDefault1"
                    class="form-check-input" style="margin-top:9px" disabled placeholder="Type something...">
                <input type="text" class="border-bottom border-0 outline-none option ms-2 p-1 px-2 w-100"
                    v-model="option.text" v-if="props.OptionProperty.multiple_select" placeholder="Option"
                    @input="setOptionText()" />
            </div>
            <div v-else-if="props.OptionProperty.tag === 'text'">
                <textarea v-if="props.OptionProperty.type === 'textarea'" class="border p-2 w-75" rows="5"
                    placeholder="Type something..." disabled style="resize: none;"></textarea>
                <input v-else-if="props.OptionProperty.type === 'text'" type="text" class="border p-2 w-75"
                    placeholder="Type something..." disabled>
            </div>
            <div v-else class="d-flex">
                <select v-if="props.OptionProperty.type === 'select'" class="border p-2 w-25 outline-none">
                    <option value="">Select...</option>
                    <option v-for="(dropdown, index) in option.dropdownSelection" :value="dropdown.text" :key="index">{{
                dropdown.text }}</option>
                </select>
                <DropdownModal :Component="props.Component" @setDropdownOptionText="getDropdownText" :formCollection="props.OptionColletion"></DropdownModal>
            </div>
        </div>
    </div>
</template>