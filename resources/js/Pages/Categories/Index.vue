<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ categories: Object })

const editId = ref(null);
const form = useForm({
    title:null,
    monthly_limit:null
});


const onSubmit = () => {
    if(editId.value) {
        form.put(`categories/${editId.value}`, { onSuccess: () => {
            form.reset()
            editId.value = null
        } })
    }else {
        form.post('/categories', { onSuccess: () => form.reset() })
    }

}

const onEdit = (category) => {
    form.clearErrors()
    editId.value = category.id
    form.title = category.title
}

const onDelete = (category) => {
    if(!confirm('Do you want to delete this record ?')) return;
    form.delete(`categories/${category.id}`)
}

</script>
<template>
    <div class="container py-5">
        <div class="col-md-6 mx-auto">
            <form @submit.prevent="onSubmit">
                <div class="mb-3">
                    <input type="text" class="form-control" name="title" id="title" placeholder="Title"
                        v-model="form.title">
                    <div v-if="form.errors.title" class="text-danger">{{ form.errors.title }}</div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Monthly limit" v-model="form.monthly_limit">
                    <div v-if="form.errors.monthly_limit" class="text-danger">{{ form.errors.monthly_limit }}</div>
                </div>


                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-primary">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Used amount</th>
                        <th scope="col">Left amount</th>
                        <th scope="col">Monthly limit</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="" v-for="category in categories">
                        <td scope="row" v-text="category.id"></td>
                        <td v-text="category.title"></td>
                        <td v-text="(category.expenses_sum_amount ?? 0)"></td>
                        <td v-text="category.monthly_limit - (category.expenses_sum_amount ?? 0)"></td>
                        <td v-text="category.monthly_limit"></td>
                        <td>
                            <div class="d-flex" style="gap: .7rem;">
                                <button type="button" class="btn btn-primary" @click="onEdit(category)">Edit</button>
                                <button type="button" class="btn btn-danger" @click="onDelete(category)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>
