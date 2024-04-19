import { ref } from "vue"
import debounce from "lodash.debounce"

export default function useAutocompleteServer() {
  const loading = ref(false)
  const endPoint = ref("")
  const items = ref([])
  const selectedItem = ref(null)

  let waitingForData = false

  const loadAutocompleteItems = (search) => {
    if (!loading.value) {
      loading.value = true
    }

    debounceLoadAutocompleteItems(search)
  }

  const debounceLoadAutocompleteItems = debounce((search) => {
    if (search) {
      if (waitingForData) return

      waitingForData = true

      axios
        .post(`${endPoint.value}/load-autocomplete-items`, { search: search })
        .then((response) => {
          items.value = response.data.autocompleteItems
          waitingForData = false
          loading.value = false
        })
    } else {
      if (selectedItem.value) {
        items.value = [selectedItem.value]
      } else {
        items.value = []
      }
      loading.value = false
    }
  }, 500)

  return {
    selectedItem,
    loading,
    endPoint,
    items,
    loadAutocompleteItems,
  }
}