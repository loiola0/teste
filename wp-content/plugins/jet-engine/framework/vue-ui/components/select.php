<cx-vui-component-wrapper
	:elementId="currentId"
	:label="label"
	:description="description"
	:wrapper-css="wrapperCss"
	:preventWrap="preventWrap"
	v-if="isVisible()"
>
	<select
		:id="currentId"
		:placeholder="placeholder"
		:disabled="disabled"
		:readonly="readonly"
		:name="name"
		:multiple="multiple"
		v-model="currentValue"
		:class="controlClasses()"
		@focus="handleFocus"
		@blur="handleBlur"
		@change="handleInput"
	>
		<option
			v-if="placeholder"
			value=""
			disabled
		>{{ placeholder }}</option>
		<template v-if="groups.length">
			<optgroup v-for="group in groups" :label="group.label">
				<option
					v-for="option in group.options"
					:value="option.value"
					:selected="isOptionSelected( option )"
				>{{ option.label }}</option>
			</optgroup>
		</template>
		<template v-else>
			<option
				v-for="option in options"
				:value="option.value"
				:selected="isOptionSelected( option )"
			>{{ option.label }}</option>
		</template>
	</select>
	<slot></slot>
</cx-vui-component-wrapper>
