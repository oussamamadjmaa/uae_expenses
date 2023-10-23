<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue';

const props = defineProps({ categories: Object, expenses: Object, stats: Object })

const editId = ref(null)

const imageUrl = ref(null);

const form = useForm({
    category_id: props.categories.length ? props.categories[0].id : null,
    payment_method: 'cash',
    amount: null,
    note: null,
    image: null,
    expense_date: new Date().toISOString().substr(0, 10)
})

const uploadForm = useForm({
    file: null
})

const uploadImage = function (event) {
    uploadForm.file = event.target.files[0]
    uploadForm.post('/upload', {
        onSuccess: (res) => {
            form.image = res.props.data.path
            imageUrl.value = res.props.data.url
            event.target.value = null
        }
    });
}

const onDelete = (expense) => {
    if(!confirm('Do you want to delete this record ?')) return;
    form.delete(`expenses/${expense.id}`)
}
</script>
<template>
    <div class="container py-5">
        <div class="col-md-6 mx-auto">
            <form @submit.prevent="form.post('', {
                onSuccess: () => {
                    form.reset()
                    form.image = imageUrl = null
                }
            })">
                <div class="mb-3">
                    <input type="date" class="form-control" placeholder="Date" v-model="form.expense_date">
                    <div v-if="form.errors.expense_date" class="text-danger">{{ form.errors.expense_date }}</div>
                </div>
                <div class="mb-3">
                    <select class="form-select form-select-lg" v-model="form.category_id">
                        <option v-for="category in categories" :value="category.id" v-text="`${category.title}`"></option>
                    </select>
                    <div v-if="form.errors.category_id" class="text-danger">{{ form.errors.category_id }}</div>
                </div>
                <div class="mb-3">
                    <select class="form-select form-select-lg" v-model="form.payment_method">
                        <option value="cash">Cash</option>
                        <option value="card">Credit/Debit Card</option>
                    </select>
                    <div v-if="form.errors.payment_method" class="text-danger">{{ form.errors.payment_method }}</div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Amount" v-model="form.amount">
                    <div v-if="form.errors.amount" class="text-danger">{{ form.errors.amount }}</div>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" placeholder="Note" v-model="form.note"></textarea>
                    <div v-if="form.errors.note" class="text-danger">{{ form.errors.note }}</div>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control" @input="uploadImage($event)" accept="image/*" />
                    <progress v-if="uploadForm.progress" :value="uploadForm.progress.percentage" max="100">
                        {{ uploadForm.progress.percentage }}%
                    </progress>
                    <div v-if="uploadForm.errors.file" class="text-danger">{{ uploadForm.errors.file }}</div>
                    <div class="mt-3" v-if="imageUrl">
                        <img :src="imageUrl" style="max-width: 100%;">
                    </div>
                </div>


                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="my-4">
            <ul class="mb-0">
                <li v-for="(stat, statKey) in stats" v-text="`${statKey}: ${stat} Dzd`"></li>
            </ul>
        </div>

        <div class="table-responsive mt-1">
            <table class="table table-primary">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Category</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment method</th>
                        <th scope="col">Note</th>
                        <th scope="col">Image</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="" v-for="expense in expenses.data">
                        <td scope="row" v-text="expense.date"></td>
                        <td v-text="expense.category.title"></td>
                        <td v-text="expense.amount"></td>
                        <td v-text="expense.payment_method"></td>
                        <td v-text="expense.note"></td>
                        <td>
                            <a v-if="expense.imageUrl" target="_blank" :href="expense.imageUrl">Show image</a>
                            <span v-else>No image</span>
                        </td>
                        <td>
                            <div class="d-flex" style="gap: .7rem;">
                                <button type="button" class="btn btn-danger" @click="onDelete(expense)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
