module.exports = {
  	methods: {
    	/**
    	* Translate the given key.
    	*/
      	__(key, replace = {}) {
          	var translation = this.$page.props.lang[key]
              	? this.$page.props.lang[key]
              	: key

          	Object.keys(replace).forEach(function (key) {
              	translation = translation.replace(':' + key, replace[key])
          	});

          	return translation
      	},
  	},
}