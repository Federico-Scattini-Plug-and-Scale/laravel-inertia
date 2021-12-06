module.exports = {
  	methods: {
    	/**
    	* Translate the given key.
    	*/
      	__(key, replace = {}) {
          	let translation = this.$page.props.lang[key]
              	? this.$page.props.lang[key]
              	: key

          	Object.keys(replace).forEach(function (key) {
              	translation = translation.replace(':' + key, replace[key])
          	});

          	return translation
      	},
		/**
    	* Get translated record from DB object.
    	*/
		trans(value) {
			let translation = value[this.$page.props.locale]

			return translation ? translation : this.__('No translation available.')
		},
  	},
}