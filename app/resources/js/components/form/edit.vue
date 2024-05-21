<script setup>
import { reactive, onMounted } from 'vue';
import axios from 'axios'
import container from '../../crafted/form/container/editcontainer.vue';
import { ValidateQuestionCollection, RemoveObject, RemoveFromCollection } from '../../composables/form'
import Preview from './preview.vue'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'


let props = defineProps({
    form: { type: Array }
})

let initialState = {
    componentCollection: [],
    counter: 1,
    dataCollection: [],
    componentkey: 0,
    formData: new FormData()
}

let form = reactive({ ...initialState })

function AddFormSection(Sectionname, required) {
    form.componentCollection.push({
        id: form.counter++,
        requiredswitch: required,
        name: Sectionname
    })
}

function FormSave() {
    Swal.fire({
        title: "Do you want to save the changes?",
        showDenyButton: true,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Publish",
        denyButtonText: `Save as draft`
    }).then((result) => {
        if (result.isConfirmed) {
            submit(true)
            Swal.fire("Successfully publish form!", "", "success");
        } else if (result.isDenied) {
            submit(false)
            Swal.fire("Successfully save as draft!", "", "success");
        }
    });


}

function submit(isPublish) {
    // let formData = new FormData()
    // for (let i = 0; form.dataCollection.length > i; i++) {
    //     formData.append('status', isPublish)
    //     formData.append('formid[]', props.form[0].id)
    //     formData.append('ids[]', form.dataCollection[i].id)
    //     formData.append('texts[]', form.dataCollection[i].text)
    //     formData.append('htmls[]', form.dataCollection[i].html)
    //     formData.append('requireds[]', form.dataCollection[i].required)
    //     formData.append('multipleselects[]', form.dataCollection[i].multipleselect)
    //     formData.append('otheroptions[]', form.dataCollection[i].otheroption)
    //     formData.append('isQuestions[]', form.dataCollection[i].isQuestion)
    //     formData.append('optiontypes[]', form.dataCollection[i].optiontype === null ? null : form.dataCollection[i].optiontype.id)
    //     formData.append('optionverticalaligns[]', form.dataCollection[i].optionverticalalign)
    //     formData.append('imagespositions[]', form.dataCollection[i].imagesposition)

    //     if (form.dataCollection[i].prerequisite !== null) {
    //         for (let j = 0; form.dataCollection[i].prerequisite.length > j; j++) {
    //             formData.append('question_id_prerequisites[]', form.dataCollection[i].id)
    //             formData.append('prerequisiteforms[]', form.dataCollection[i].prerequisite[j].prerequisiteform)
    //             formData.append('prerequisitequestions[]', form.dataCollection[i].prerequisite[j].prerequisitequestion)
    //             formData.append('prerequisiteanswers[]', form.dataCollection[i].prerequisite[j].prerequisiteanswer)
    //             formData.append('questionids[]', form.dataCollection[i].prerequisite[j].questionid)
    //         }
    //     }
    //     if (form.dataCollection[i].images !== null) {
    //         for (let j = 0; form.dataCollection[i].images.length > j; j++) {
    //             formData.append('question_id_images[]', form.dataCollection[i].id)
    //             formData.append('heights[]', form.dataCollection[i].images[j].height)
    //             formData.append('widths[]', form.dataCollection[i].images[j].width)
    //             formData.append('upload_files[]', form.dataCollection[i].images[j].url)
    //         }
    //     }
    //     if (form.dataCollection[i].options !== null) {
    //         for (let j = 0; form.dataCollection[i].options.length > j; j++) {
    //             formData.append('question_ids[]', form.dataCollection[i].options[j].questionid)
    //             formData.append('optiontexts[]', form.dataCollection[i].options[j].text)
    //         }
    //     }
    // }
    // axios.post('/form/section/store', formData).then(function (response) {
    //     window.location.href = response.data.redirect
    // })
}
function getSectionProperties(collection) {
    let validatecollection = ValidateQuestionCollection(form.dataCollection, collection[0].id)[0]
    if (validatecollection === undefined) {
        form.dataCollection.push({
            id: collection[0].id,
            text: collection[0].text,
            html: collection[0].html,
            options: collection[0].options,
            required: collection[0].required,
            images: collection[0].images,
            multipleselect: collection[0].multipleselect,
            otheroption: collection[0].otheroption,
            isQuestion: collection[0].isquestion,
            optiontype: collection[0].optiontype,
            imagesposition: collection[0].imagesposition,
            optionverticalalign: collection[0].optionverticalalign,
            prerequisite: collection[0].prerequisite
        })
    }
    else {
        validatecollection.text = collection[0].text,
            validatecollection.html = collection[0].html,
            validatecollection.options = collection[0].options,
            validatecollection.required = collection[0].required
        validatecollection.images = collection[0].images,
            validatecollection.multipleselect = collection[0].multipleselect,
            validatecollection.otheroption = collection[0].otheroption,
            validatecollection.optiontype = collection[0].optiontype,
            validatecollection.imagesposition = collection[0].imagesposition,
            validatecollection.optionverticalalign = collection[0].optionverticalalign,
            validatecollection.prerequisite = collection[0].prerequisite
    }
}
function getRemoveComponent(id) {
    RemoveObject('section' + id)
    form.dataCollection = RemoveFromCollection(form.dataCollection, id)
}

function getFormSection(sections) {
    sections.forEach(item => {
        if (item.is_question) {
            AddFormSection('question', true)
        }
        else if (item.is_question || item.text === null) {
            AddFormSection('image', false)
        }
        else {
            AddFormSection('text', false)
        }

        form.dataCollection.push({
            id: item.id,
            text: item.text,
            html: item.html,
            options: item.options,
            required: item.is_required,
            images: getFormImage(item.images),
            multipleselect: item.is_multiple_select,
            otheroption: item.with_other_option,
            isQuestion: item.is_question,
            optiontype: item.option_types,
            imagesposition: item.image_position,
            optionverticalalign: item.option_vertical_align,
            prerequisite: getPrerequisite(item.prerequisites)
        })
        console.log(item.prerequisites)
        console.log(getPrerequisite(item.prerequisites))
    });
}

function getFormImage(images) {
    let imageCollection = []
    images.forEach(item => {
        imageCollection.push({
            questionid: item.form_id,
            id: item.id,
            width: item.width,
            height: item.height,
            url: item.path,
            name: item.filename
        })
    });

    return imageCollection

}

function getPrerequisite(prerequisite) {
    let prerequisiteCollection = []
    prerequisite.forEach(item => {
        prerequisiteCollection.push({
            questionid: item.form_id,
            prerequisiteform: item.section_id,
            prerequisitequestion: item.prerequisite_form_id,
            prerequisiteanswer: item.answer
        })
    });
    return prerequisiteCollection;
    
}
onMounted(() => {
    props.form.forEach(item => {
        getFormSection(item.sections)
    });

})
</script>
<template>
    <div class="container">
        <div class="container-fluid my-5" style="height: 90vh;">
            <div class="row mt-2 h-100">
                <div class="col h-100" style="max-height:90vh;overflow-y:scroll">
                    <container :Component="component" v-for="(component, index) in form.componentCollection"
                        class="mb-2 border bg-white" :id="'section' + (index + 1)"
                        @SectionProperty="getSectionProperties" @RemoveComponent="getRemoveComponent"
                        :FormDetails="props.form" :FormCollection="form.dataCollection" :Index="index"></container>
                </div>
                <div class="col-1">
                    <button class="btn-icon d-block" title="Add question" @click="AddFormSection('question', true)"><i
                            class="bi bi-plus-circle-fill"></i></button>
                    <button class="btn-icon d-block" title="Add image" @click="AddFormSection('image', false)"><i
                            class="bi bi-image-fill"></i></button>
                    <button class="btn-icon d-block" title="Add text" @click="AddFormSection('text', false)"><i
                            class="bi bi-fonts"></i></button>
                    <Preview :FormContent="form.dataCollection" :Form="props.form"></Preview>
                    <button class="btn-icon d-block" title="Add text" @click="FormSave()"><i
                            class="bi bi-save2"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>