<cx-vui-component-wrapper
	:elementId="currentId"
	:label="label"
	:description="description"
	:wrapper-css="wrapperCss"
	:preventWrap="preventWrap"
	v-if="isVisible()"
>
	<textarea
		:id="currentId"
		:class="controlClasses"
		:placeholder="placeholder"
		:disabled="disabled"
		:rows="rows"
		:readonly="readonly"
		:name="name"
		:value="currentValue"
		@keyup.enter="handleEnter"
		@keyup="handleKeyup"
		@keypress="handleKeypress"
		@keydown="handleKeydown"
		@focus="handleFocus"
		@blur="handleBlur"
		@input="handleInput"
		@change="handleChange"
	></textarea>
	<slot></slot>
</cx-vui-component-wrapper>
