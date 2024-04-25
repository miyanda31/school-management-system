<template>
    <Layout>
        <Head>
            <title>Eduket | Profile</title>

        </Head>
        <page-header current-page="User Profile" page="Profile"/>
        <div class="row">
            <div v-for="category in categories" class="col-md-4 col-xl-4">
                <div class="card card-box mb-30">
                    <div class="card-body">
                        <h5 class="card-title">{{category.name}}</h5>
                        <div style="display: none" :id="category.name.split(' ').join('-').toLocaleLowerCase()" class="alert alert-success alert-dismissible fade show"  role="alert" >
                            User details updated successfully
                            <button @click="setCategory(category.name.split(' ').join('-').toLocaleLowerCase())" type="button" class="close"  aria-label="Close" >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <table v-if="category.field.length > 0" class="table-borderless table table-sm">
                            <tr v-for="field in category.field">
                                <th>{{field.name}}</th>
                                <th>
                                    <p>
                                        <a @click="setCategory(category.name.split(' ').join('-').toLocaleLowerCase())" href="javascript:void(0)" :id="field.name.split(' ').join('-').toLocaleLowerCase()" :data-title="'Enter '+field.name" class="editable editable-click" data-original-title="" :title="field.name">
                                            {{field.biography && field.biography.name ?field.biography.name.trim():''}}
                                        </a>
                                    </p>

                                </th>
                            </tr>
                        </table>
                        <length message="No fields have been added to this section yet. Wait for admins to update this section" :category="category.field" />
                    </div>
                </div>
            </div>

        </div>
        <div v-if="categories.length === 0" class="card-box pd-20 height-100-p mb-30">
            <length message="No profile categories have been created for users. Please create them in settings before checking out" :category="categories" />
        </div>
    </Layout>
</template>

<script>
export default {
    name: "Profile",
    props: {
        categories:[],
        profile:{}
    },
    data() {
        return {
            id: ''
        }
    },
    mounted() {
        var vm = this
            $(document).ready(function () {

                $.ajax({
                    url:'/js/bootstrap-editable.min.js',
                    cache:true,
                    dataType:'script',
                    success:function(){
                        vm.categories.forEach(category=>{
                            category.field.forEach(field=>{
                                vm.loadEditable(field)
                            })
                        })
                    }
                })
            })

    },

    methods: {
        setCategory(cat) {
            if (cat !== this.id) this.id = cat
            $('#'+this.id).css({display: 'none'})
        },
        createField(value) {
            this.$inertia.post(this.$route('profile.store'),{id:this.profile.id,f:value.pk,value:value.value},{
                preserveScroll:true,
                preserveState:true,
                onSuccess:()=>{
                    $('#'+this.id).css({display: 'block'})
                }
            })
        },

        loadEditable(field){
            var id = $('#'+field.name.split(' ').join('-').toLocaleLowerCase());
            switch (field.type) {
                case 'Text':
                    id.editable({
                        url: this.createField,
                        type: 'text',
                        pk: field.id,
                        validate: function(value) {
                            if (field.required === 1) {
                                if($.trim(value) === '') return 'This field is required';
                            }

                        }
                    })
                    break;
                case 'Date':
                    id.editable({
                        url: this.createField,
                        type: 'date',
                        pk: field.id,
                        format: 'yyyy-mm-dd',
                        viewformat: 'dd/mm/yyyy',
                        datetimepicker: {
                            todayBtn: 'linked',
                            weekStart: 1
                        },
                        validate: function(value) {
                            if (field.required === 1) {
                                if($.trim(value) === '') return 'This field is required';
                            }

                        }

                    })
                    break
                case 'Options':
                    id.editable({
                        url: this.createField,
                        type: 'select',
                        pk: field.id,
                        prepend: "No selection",
                        source: field.value? field.value.replaceAll('"','').split(','):[],
                        validate: function(value) {
                            if (field.required === 1) {
                                if($.trim(value) === '') return 'This field is required';
                            }

                        }
                    })
                    break

                case 'Large Text':
                    id.editable({
                        url: this.createField,
                        type: 'textarea',
                        pk: field.id,
                        validate: function(value) {
                            if (field.required === 1) {
                                if($.trim(value) === '') return 'This field is required';
                            }

                        }
                    })
                    break

                case 'Yes/No':
                    id.editable({
                        url: this.createField,
                        type: 'select',
                        pk: field.id,
                        prepend: "No selection",
                        source: ['Yes','No'],
                    })
                    break

            }
        }
    },
}
</script>

