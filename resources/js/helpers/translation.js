export default function __(pageProp, key, replace = {}) {
  let translation = pageProp[key]
      ? pageProp[key]
      : key

  Object.keys(replace).forEach(function (key) {
      translation = translation.replace(':' + key, replace[key])
  });

  return translation
}