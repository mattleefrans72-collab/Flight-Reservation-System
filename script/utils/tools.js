export const debounce = (fn, delay) => {
    let id;
    console.log(`id at immediate load: ${id}`);
    return ( ... args) => {
      console.log(id)
      if (id) 
        {clearTimeout(id)};
      id = setTimeout(() => {
        fn( ... args);
      }, delay);
    } 
  }