<div>
    {{$this->table}}

    <script>
        setInterval(()=>{
            Livewire.dispatch('refresh');
        },60000)
    </script>
</div>
