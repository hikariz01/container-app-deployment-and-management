<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <div class="btn btn-success" @click="shownumber(5)">
                            Plus 5
                        </div>
                        <div class="btn btn-success" @click="shownumber(10)">
                            Plus 10
                        </div>
                        <a href="/create" class="btn btn-warning">
                            Go to create
                        </a>
                        I'm an example component.
                        {{number}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data() {
          return {
              number: 0
          }
        },
        methods: {
            shownumber: function (howmany) {
                this.number += howmany
            }
        }
    }

</script>
