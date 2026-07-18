namespace CrmApp.Data.Models;

public class Notification
{
    public int Id { get; set; }
    public string Type { get; set; } = string.Empty;
    public string Data { get; set; } = string.Empty;
    public bool IsRead { get; set; }
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
    public DateTime? ReadOn { get; set; }
}