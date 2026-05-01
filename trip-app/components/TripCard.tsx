import Link from "next/link";

type TripCardProps = {
  id: number;
  title: string;
};

export default function TripCard({ id, title }: TripCardProps) {
  return (
    <Link href={`/trips/${id}`}>
      <div className="border rounded p-4 mt-4 cursor-pointer hover:bg-gray-50">
        <h2 className="text-lg font-semibold">{title}</h2>
        <p className="text-sm text-gray-500">旅行の詳細を見る</p>
      </div>
    </Link>
  );
}
