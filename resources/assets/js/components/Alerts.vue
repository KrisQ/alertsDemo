<template>
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h2>Alerts</h2>
				</div>
				<div class="card-body">
					<div v-if="message !== ''" @click="fetchAlerts()" class="alert alert-info" role="alert" v-text="message"></div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Customer</th>
								<th>Date Range</th>
								<th>Short Description</th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="alert in alerts">
								<td><p v-for="customer in customers" :hidden="customer.id !== alert.customer_id">{{customer.name}}</p></td>
								<td>{{alert.date_range}}</td>
								<td :class="[alert.status === 1 ? 'text-danger' : 'text-success']">{{alert.short_description}}</td>
								<td><button @click="changeCurrentAlert(alert)" class="btn btn-outline-info btn-block">Select</button></td>
							</tr>
						</tbody>
					</table>


					<nav aria-label="...">
					  <ul class="pagination">
					    <li :class="[{disabled: !pagination.prev_page_url}]" class="page-item">
					      <a class="page-link" href="#" @click="fetchAlerts(pagination.prev_page_url)" tabindex="-1">Previous</a>
					    </li>
					    <li class="page-item disabled text-dark">
					    	<a class="page-link text-dark" href="">Page {{pagination.current_page}} of {{pagination.last_page}}</a>
					    </li>
					    <li :class="[{disabled: !pagination.next_page_url}]" class="page-item">
					      <a class="page-link" @click="fetchAlerts(pagination.next_page_url)" href="#">Next</a>
					    </li>
					  </ul>
					</nav>


				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h2>Alert</h2>
				</div>
				<div class="card-body">
					<button :hidden="this.edit === false" class="btn btn-outline-info btn-block" @click="createAlertIsTrue()">Add New Alert</button>
					<br>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Short Description" v-model="alert.short_description">
					</div>
					<div class="form-group">
						<textarea class="form-control" placeholder="Long Description" v-model="alert.long_description"></textarea>
					</div>
					<div class="form-group">
						<input type="date" class="form-control" placeholder="Short Description" v-model="alert.date_range">
					</div>
					<div class="form-group">
						<select class="form-control" v-model="alert.status">
							<option :selected="alert.status === 1" value="1">Active</option>
							<option :selected="alert.status === 2" value="2">Innactive</option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" v-model="alert.customer_id">
							<option v-for="customer in customers" :selected="customer.id === alert.customer_id" :value="customer.id">{{customer.name}}</option>
						</select>
					</div>
					<div :hidden="this.edit === true">
						<button @click="createAlert()" class="btn btn-block btn-outline-info">Create</button>
					</div>
					<div :hidden="this.edit === false">
						<div class="row">
							<div class="col-md-6"><button @click="updateAlert(alert)" class="btn btn-block btn-outline-info">Edit</button></div>
							<div class="col-md-6"><button @click="deleteAlert(alert.id)"class="btn btn-block btn-outline-info">Delete</button></div>
						</div>
					</div>
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
				alert : {
					id: '',
					customer_id: '',
					user_id: '',
					short_description: '',
					long_description: '',
    				status: ''
				},
				customers: [],
				user: '',
				alert_id: '',
				pagination: {},
				edit: false,
				message: ''
			}
		},
		created(){
			this.fetchAlerts();
		},
		methods: {
			fetchAlerts(page_url){
				this.message = '';
				let vm = this;
				page_url = page_url || 'api/alert/'
				axios.get(page_url)
					  .then(response => {
					  	this.user = response.data.user;
					    this.alerts = response.data.alerts.data;
					    this.customers = response.data.customers;
					    console.log(response.data.alerts.current_page);
					   	vm.makePagination(response.data.alerts)
					  })
					  .catch(error => console.log(error));
			},
			changeCurrentAlert(alert){
				this.alert = alert;
				this.edit = true;
				this.alert_id = alert.id;
			},
			createAlertIsTrue(){
				this.edit = false;
				this.alert = {
					id: '',
					customer_id: '',
					user_id: '',
					short_description: '',
					long_description: '',
    				status: ''
				}
			},
			createAlert(){
				console.log('sdljaskjdaksjd');
				axios.post('api/alert/create', {
					customer_id: this.alert.customer_id,
					user_id: this.user.id,
					short_description: this.alert.short_description,
					long_description: this.alert.long_description,
					date_range: this.alert.date_range,
					status: this.alert.status
				})	
				.then(data => {
					this.createAlertIsTrue()
					this.fetchAlerts();
				})
				.catch(error => {
					console.log(error);
					this.message = "All Fields are required"
				});
			},
			updateAlert(){
				axios.post('api/alert/update', {
						id: this.alert.id,
						customer_id: this.alert.customer_id,
						user_id: this.user.id,
						short_description: this.alert.short_description,
						long_description: this.alert.long_description,
						date_range: this.alert.date_range,
						status: this.alert.status
					})	
					.then(data => {
						this.createAlertIsTrue()
						this.fetchAlerts();
					})
					.catch(error => {
						console.log(error);
						this.message = "All Fields are required"
					});
			},
			deleteAlert(id){
					axios.delete(`api/alert/${id}/delete`)
						.then(response => {
							this.createAlertIsTrue()
							this.fetchAlerts();
						})
						.catch(error => console.log(error));
			},
			makePagination(alerts){
				let pagination = {
					current_page: alerts.current_page,
					last_page: alerts.last_page,
					next_page_url: alerts.next_page_url,
					prev_page_url: alerts.prev_page_url
				};
				this.pagination = pagination;
			},

		}
	}
</script>