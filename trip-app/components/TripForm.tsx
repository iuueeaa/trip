type TripFormProps = {
  onAdd: (title: string) => void;
};

export default function TripForm({ onAdd }: TripFormProps) {
  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const form = e.currentTarget;
    const input = form.elements.namedItem("title") as HTMLInputElement;

    if (!input.value) return;

    onAdd(input.value);
    input.value = "";
  };

  return (
    <form onSubmit={handleSubmit} className="flex gap-2 mt-4">
      <input name="title" className="border p-2" placeholder="旅行名を入力" />
      <button className="bg-blue-500 text-white px-4">追加</button>
    </form>
  );
}
