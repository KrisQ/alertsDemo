<template>
<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h2>Alerts</h2>
				</div>
				<div class="card-body">
				  <!-- <v-server-table :options="options" url="api/alert/alerts" :columns="columns"></v-server-table> -->
                    <v-client-table 
                        :data="this.alerts" 
                        :columns="columns"
                        :options="options"
                    >
                          <!-- <a slot="edit" slot-scope="props" class="fa fa-edit" :href="edit(props.row.id)"></a> -->
                          <button class="btn btn-outline-warning" slot="edit" slot-scope="props" @click="edit(props.row)">Edit</button>

                    </v-client-table>

				</div>
			</div>
		</div>
	</div>
</template>


<script>

	export default {
		data() {
			return {
                alerts: [],
                table: [],
                customers: [],
                user: '',
                columns: [
                    'id',
                    'user_id',
                    'customer_id',
                    'date_range',
                    'short_description',
                    'long_description',
                    'status',
                    'created_at',
                    'updated_at',
                    'edit'
                ],
                options: {
                    sortable: [
                    'id',
                    'user_id',
                    'customer_id',
                    'date_range',
                    'short_description',
                    'long_description',
                    'status',
                    'created_at',
                    'updated_at'
                    ]
                }
			}
        },
        created(){
            this.fetchAlerts();
        },

        methods: {
			fetchAlerts(page_url){
				let vm = this;
				page_url = page_url || 'api/alert/all'
				axios.get(page_url)
					  .then(response => {
					  	this.user = response.data.user;
					    this.alerts = response.data.alerts;
                        this.customers = response.data.customers;
                        this.alerts.forEach(alert => {
                            this.table.push({id: alert.id})
                        });
					    // console.log(response);
                           // vm.makePagination(response.data.alerts)
					  })
                      .catch(error => console.log(error));
            },
            edit(alert){
                console.log(alert);
            }
		}
	}
</script>
