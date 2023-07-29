export const showInvalidFeedback = (input) => show(input.nextElementSibling);
export const hideInvalidFeedback = (input) => hide(input.nextElementSibling);
export const hide = (el) => el.classList.add('hidden');
export const show = (el) => el.classList.remove('hidden');

export const validateInput = (e, validate) => {
  if (validate(e.target.value)) {
      hideInvalidFeedback(e.target);
  } else {
      showInvalidFeedback(e.target);
  }
}
