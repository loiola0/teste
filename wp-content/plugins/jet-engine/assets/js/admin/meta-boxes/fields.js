Vue.component( 'jet-meta-fields', {
	name: 'jet-meta-fields',
	template: '#jet-meta-fields',
	props: {
		value: {
			type: Array,
			default: function() {
				return [];
			},
		},
		hideOptions: {
			type: Array,
			default: function() {
				return [];
			},
		},
		slugDelimiter: {
			type: String,
			default: function() {
				return '-';
			},
		},
	},
	data: function() {
		return {
			fieldsList: this.value,
			fieldTypes: JetEngineFieldsConfig.field_types,
			postTypes: JetEngineFieldsConfig.post_types,
			blockTitle: JetEngineFieldsConfig.title,
			buttonLabel: JetEngineFieldsConfig.button,
			disabledFields: JetEngineFieldsConfig.disabled,
			glossariesList: JetEngineFieldsConfig.glossaries,
			i18n: JetEngineFieldsConfig.i18n,
		};
	},
	watch: {
		value: function( val ) {
			var openedTab = false;

			for ( var i = 0; i < val.length; i++ ) {
				switch ( val[i].object_type ) {
					case 'field':
						val[i].isNested = openedTab;
						break;

					case 'tab':
					case 'accordion':
						openedTab = true;
						val[i].isNested = false;
						break;

					case 'endpoint':
						openedTab = false;
						val[i].isNested = false;
						break;
				}
			}

			this.fieldsList = val;
		},
		fieldsList: {
			handler: function( val ) {
				this.$emit( 'input', val );
			},
			deep: true,
		},
	},
	computed: {
		repeaterFieldTypes: function() {
			var blackList = [ 'repeater', 'html' ];
			return this.fieldTypes.filter( function( field ) {
				return 0 > blackList.indexOf( field.value );
			} );
		},
		fieldsNames: function() {
			var result = [];

			for ( var i = 0; i < this.fieldsList.length; i++ ) {
				result.push( this.fieldsList[i].name );
			}

			return result;
		},
		fieldsOptionList: function() {
			var result = [],
				blackTypesList = [ 'repeater', 'media', 'gallery', 'posts', 'iconpicker', 'html' ];

			for ( var i = 0; i < this.fieldsList.length; i++ ) {

				if ( 'field' !== this.fieldsList[i].object_type ) {
					continue;
				}

				if ( -1 !== blackTypesList.indexOf( this.fieldsList[i].type ) ) {
					continue;
				}

				result.push( {
					value: this.fieldsList[i].name,
					label: this.fieldsList[i].title,
				} );
			}

			return result;
		}
	},
	methods: {
		onInput: function() {
			this.$emit( 'input', this.fieldsList );
		},
		getFieldSubtitle: function( field ) {

			var result = field.name + ' (';

			if ( 'field' === field.object_type ) {
				result += field.type;
			} else {
				result += field.object_type;
			}

			result += ')';

			return result;

		},
		addNewField: function( event ) {

			var field = {
				title: '',
				name: '',
				object_type: 'field',
				width: '100%',
				options: [],
				type: 'text',
				collapsed: false,
				id: this.getRandomID(),
			};

			this.fieldsList.push( field );
			//this.onInput();

		},
		setFieldProp: function( index, key, value ) {

			var field = this.fieldsList[ index ];

			field[ key ] = value;

			this.fieldsList.splice( index, 1, field );
			//this.onInput();

		},
		preSetFieldName: function( index ) {

			var field = this.fieldsList[ index ];

			if ( ! field.name && field.title ) {
				field.name = field.title;
				this.fieldsList.splice( index, 1, field );
				this.sanitizeFieldName( index );
				//this.onInput();
			}

		},
		sanitizeFieldName: function( index ) {

			var field = this.fieldsList[ index ],
				names = this.fieldsNames;

			var regex = /\s+/g;
			field.name = field.name.toLowerCase().replace( regex, this.slugDelimiter );
			field.name = window.JetEngineTools.maybeCyrToLatin( field.name );

			names.splice( index, 1 );

			if ( -1 !== names.indexOf( field.name ) ) {
				field.name = field.name + '_' + Math.floor( Math.random() * Math.floor( 999 ) );
			}

			this.fieldsList.splice( index, 1, field );
		},
		preSetRepeaterFieldName: function( fieldIndex, repeaterFieldIndex ) {

			var field         = this.fieldsList[ fieldIndex ],
				repeaterField = field['repeater-fields'][ repeaterFieldIndex ];

			if ( ! repeaterField.name && repeaterField.title ) {
				repeaterField.name = repeaterField.title;
				field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
				this.fieldsList.splice( fieldIndex, 1, field );
				this.sanitizeRepeaterFieldName( fieldIndex, repeaterFieldIndex );
				//this.onInput();
			}

		},
		sanitizeRepeaterFieldName: function( fieldIndex, repeaterFieldIndex ) {

			var field          = this.fieldsList[ fieldIndex ],
				repeaterField  = field['repeater-fields'][ repeaterFieldIndex ],
				needModifyName = false;

			var regex = /\s+/g;
			repeaterField.name = repeaterField.name.toLowerCase().replace( regex, this.slugDelimiter );
			repeaterField.name = window.JetEngineTools.maybeCyrToLatin( repeaterField.name );

			for ( var i = 0; i < field['repeater-fields'].length; i++ ) {

				if ( i === repeaterFieldIndex ) {
					continue;
				}

				if ( field['repeater-fields'][i].name === repeaterField.name ) {
					needModifyName = true;
					break;
				}
			}

			if ( needModifyName ) {
				repeaterField.name = repeaterField.name + '_' + Math.floor( Math.random() * Math.floor( 999 ) );
			}

			field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
			this.fieldsList.splice( fieldIndex, 1, field );
		},
		setOptionProp: function( fieldIndex, optionIndex, key, value ) {

			var field  = this.fieldsList[ fieldIndex ],
				option = field.options[ optionIndex ];

			if ( 'is_checked' === key && ( 'radio' === field.type || ( 'select' === field.type && ! field.is_multiple ) ) ) {
				for ( var i = 0; i < field.options.length; i++ ) {
					if ( field.options[ i ].is_checked ) {
						field.options[ i ].is_checked = false;
					}
				}
			}

			option[ key ] = value;

			field.options.splice( optionIndex, 1, option );
			this.fieldsList.splice( fieldIndex, 1, field );
			//this.onInput();

		},
		getOptionSubtitle: function( option ) {

			var result = option.key;

			if ( option.is_checked ) {
				result += ' (checked)';
			}

			return result;

		},
		setRepeaterFieldProp: function( fieldIndex, repeaterFieldIndex, key, value ) {

			var field         = this.fieldsList[ fieldIndex ],
				repeaterField = field['repeater-fields'][ repeaterFieldIndex ];

			repeaterField[ key ] = value;

			field['repeater-fields'].splice( repeaterFieldIndex, 1, repeaterField );
			this.fieldsList.splice( fieldIndex, 1, field );
			//this.onInput();

		},
		cloneField: function( index ) {

			var field    = JSON.parse( JSON.stringify( this.fieldsList[index] ) ),
				newField = {
					'title':                 field.title + ' (Copy)',
					'name':                  field.name + '_copy',
					'object_type':           field.object_type,
					'tab_layout':            field.tab_layout,
					'allow_custom':          field.allow_custom,
					'save_custom':           field.save_custom,
					'options_from_glossary': field.options_from_glossary,
					'glossary_id':           field.glossary_id,
					'options':               field.options,
					'type':                  field.type,
					'repeater-fields':       field['repeater-fields'],
					'description':           field.description,
					'width':                 field.width,
					'is_timestamp':          field.is_timestamp,
					'search_post_type':      field.search_post_type,
					'is_multiple':           field.is_multiple,
					'default_val':           field.default_val,
					'placeholder':           field.placeholder,
					'is_required':           field.is_required,
					'is_array':              field.is_array,
					'min_value':             field.min_value,
					'max_value':             field.max_value,
					'step_value':            field.step_value,
					'value_format':          field.value_format,
					'conditional_logic':     field.conditional_logic,
					'conditions':            field.conditions,
					'id':                    this.getRandomID(),
				};

			//this.onInput();

			this.fieldsList.splice( index + 1, 0, newField );

		},
		deleteField: function( index ) {

			// Remove conditions dependency
			this.deleteConditionsDependency( this.fieldsList[index].name );

			this.fieldsList.splice( index, 1 );
		},
		cloneOption: function( optionIndex, fieldIndex ) {

			var field     = this.fieldsList[ fieldIndex ],
				option    = field.options[ optionIndex ],
				newOption = {
					key: option.key + '_copy',
					value: option.value + '(Copy)',
					id: this.getRandomID(),
				};

			field.options.splice( optionIndex + 1, 0, newOption );

			this.fieldsList.splice( fieldIndex, 1, field );
			//this.onInput();

		},
		deleteOption: function( optionIndex, fieldIndex ) {
			this.fieldsList[ fieldIndex ].options.splice( optionIndex, 1 );
		},
		addNewFieldOption: function( $event, index ) {

			var option = {
				key: '',
				value: '',
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.fieldsList[ index ].options ) {
				this.fieldsList[ index ].options = [];
			}

			this.fieldsList[ index ].options.push( option );

			//this.onInput();

		},

		cloneRepeaterField: function( childIndex, fieldIndex ) {

			var field         = this.fieldsList[ fieldIndex ],
				repeaterField = JSON.parse( JSON.stringify( field['repeater-fields'][ childIndex ] ) ),
				newField      = {
					title:            repeaterField.title + ' (Copy)',
					name:             repeaterField.name + '_copy',
					type:             repeaterField.type,
					is_timestamp:     repeaterField.is_timestamp,
					options:          repeaterField.options,
					search_post_type: repeaterField.search_post_type,
					is_multiple:      repeaterField.is_multiple,
					is_array:         repeaterField.is_array,
					min_value:        repeaterField.min_value,
					max_value:        repeaterField.max_value,
					step_value:       repeaterField.step_value,
					value_format:     repeaterField.value_format,
					id:               this.getRandomID(),
				};

			field['repeater-fields'].splice( childIndex + 1, 0, newField );

			this.fieldsList.splice( fieldIndex, 1, field );
			//this.onInput();

		},
		deleteRepeaterField: function( childIndex, fieldIndex ) {
			this.fieldsList[ fieldIndex ]['repeater-fields'].splice( childIndex, 1 );
			//this.onInput();
		},
		addNewRepeaterField: function( $event, index ) {

			var field = {
				title: '',
				name: '',
				type: 'text',
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.fieldsList[ index ]['repeater-fields'] ) {
				this.$set( this.fieldsList[ index ], 'repeater-fields', [] );
			}

			this.fieldsList[ index ]['repeater-fields'].push( field );
			//this.onInput();

		},

		addNewRepeaterFieldOption: function( $event, rIndex, index ) {
			var option = {
				key: '',
				value: '',
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.fieldsList[ index ]['repeater-fields'][ rIndex ].options ) {
				this.$set( this.fieldsList[ index ]['repeater-fields'][ rIndex ], 'options', [] );
			}

			this.fieldsList[ index ]['repeater-fields'][ rIndex ].options.push( option );
			//this.onInput();
		},
		cloneRepeaterFieldOption: function( optionIndex, rFieldIndex, fieldIndex ) {
			var field     = this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ],
				option    = field.options[ optionIndex ],
				newOption = {
					key: option.key + '_copy',
					value: option.value + '(Copy)',
					id: this.getRandomID(),
				};

			field.options.splice( optionIndex + 1, 0, newOption );

			this.fieldsList[ fieldIndex ]['repeater-fields'].splice( rFieldIndex, 1, field );
			//this.onInput();
		},
		deleteRepeaterFieldOption: function( optionIndex, rFieldIndex, fieldIndex ) {
			this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ].options.splice( optionIndex, 1 );
		},
		setRepeaterFieldOptionProp: function( fieldIndex, rFieldIndex, optionIndex, key, value ) {
			var field  = this.fieldsList[ fieldIndex ]['repeater-fields'][ rFieldIndex ],
				option = field.options[ optionIndex ];

			if ( 'is_checked' === key && ( 'radio' === field.type || ( 'select' === field.type && ! field.is_multiple ) ) ) {
				for ( var i = 0; i < field.options.length; i++ ) {
					if ( field.options[ i ].is_checked ) {
						field.options[ i ].is_checked = false;
					}
				}
			}

			option[ key ] = value;

			field.options.splice( optionIndex, 1, option );

			this.fieldsList[ fieldIndex ]['repeater-fields'].splice( rFieldIndex, 1, field );
			//this.onInput();
		},

		addNewCondition: function( $event, index ) {
			var condition = {
				field: '',
				operator: '',
				value: '',
				values: [],
				collapsed: false,
				id: this.getRandomID(),
			};

			if ( ! this.fieldsList[ index ].conditions ) {
				this.$set( this.fieldsList[ index ], 'conditions', [] );
			}

			this.fieldsList[ index ].conditions.push( condition );
		},
		cloneCondition: function( conditionIndex, fieldIndex ) {
			var field         = this.fieldsList[ fieldIndex ],
				condition     = JSON.parse( JSON.stringify( field.conditions[ conditionIndex ] ) ),
				newCondition = {
					field:    condition.field,
					operator: condition.operator,
					value:    condition.value,
					values:   condition.values,
					id:       this.getRandomID(),
				};

			field.conditions.splice( conditionIndex + 1, 0, newCondition );
			this.fieldsList.splice( fieldIndex, 1, field );
		},
		deleteCondition: function( conditionIndex, fieldIndex ) {
			this.fieldsList[ fieldIndex ].conditions.splice( conditionIndex, 1 );
		},
		setConditionProp: function( fieldIndex, conditionIndex, key, value ) {
			var field     = this.fieldsList[ fieldIndex ],
				condition = field.conditions[ conditionIndex ];

			condition[ key ] = value;

			field.conditions.splice( conditionIndex, 1, condition );
			this.fieldsList.splice( fieldIndex, 1, field );
		},
		getConditionFieldsList: function( index ) {
			var optionsList = this.fieldsOptionList,
				currentFieldName = this.fieldsList[ index ].name;

			optionsList = optionsList.filter( function( item ) {
				return item.value !== currentFieldName;
			} );

			optionsList.unshift( {
				value: '',
				label: this.i18n.select_field,
			} );

			return optionsList;
		},
		getConditionValuesList: function( fieldIndex, conditionIndex ) {
			var selectedField = this.fieldsList[ fieldIndex ].conditions[ conditionIndex ].field,
				selectedFieldIndex,
				selectedFieldOptions,
				result = [];

			if ( ! selectedField ) {
				return result;
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return result;
			}

			selectedFieldOptions = this.fieldsList[ selectedFieldIndex ].options;

			for ( var i = 0; i < selectedFieldOptions.length; i++ ) {
				result.push( {
					value: selectedFieldOptions[i].key,
					label: selectedFieldOptions[i].value,
				} )
			}

			return result;
		},
		getConditionFieldType: function( fieldIndex, conditionIndex ) {
			var selectedField = this.fieldsList[ fieldIndex ].conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return '';
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return '';
			}

			return this.fieldsList[ selectedFieldIndex ].type;
		},
		isGlossaryField: function( fieldIndex, conditionIndex ) {
			var selectedField = this.fieldsList[ fieldIndex ].conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return '';
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return false;
			}

			return this.fieldsList[ selectedFieldIndex ].options_from_glossary;
		},
		getConditionFieldTitle: function( fieldIndex, conditionIndex ) {
			var selectedField = this.fieldsList[ fieldIndex ].conditions[ conditionIndex ].field,
				selectedFieldIndex;

			if ( ! selectedField ) {
				return '';
			}

			selectedFieldIndex = this.fieldsNames.indexOf( selectedField );

			if ( -1 === selectedFieldIndex ) {
				return '';
			}

			return this.fieldsList[ selectedFieldIndex ].title;
		},
		deleteConditionsDependency: function( fieldName ) {
			for ( var i = 0; i < this.fieldsList.length; i++ ) {

				if ( 'field' !== this.fieldsList[i].object_type ) {
					continue;
				}

				if ( undefined === this.fieldsList[i].conditions ) {
					continue;
				}

				if ( !this.fieldsList[i].conditions.length ) {
					continue;
				}

				for ( var j = 0; j < this.fieldsList[i].conditions.length; j++ ) {

					if ( fieldName !== this.fieldsList[i].conditions[j].field ) {
						continue;
					}

					this.fieldsList[i].conditions.splice( j, 1 );
				}
			}
		},

		isCollapsed: function( object ) {
			if ( undefined === object.collapsed || true === object.collapsed ) {
				return true;
			} else {
				return false;
			}
		},

		isNestedField: function( field ) {
			if ( undefined !== field.isNested && field.isNested ) {
				return true;
			}

			return false;
		},

		getRandomID: function() {
			return Math.floor( Math.random() * 8999 ) + 1000;
		},
	},
} );
